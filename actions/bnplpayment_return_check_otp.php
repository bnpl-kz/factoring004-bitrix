<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bnpl\Payment\Config;
use Bitrix\Sale\Order;
use Bnpl\Payment\DebugLoggerFactory;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Otp\CheckOtpReturn;
use BnplPartners\Factoring004\Transport\GuzzleTransport;

define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
define("NO_AGENT_CHECK", true);
define("NOT_CHECK_PERMISSIONS", true);
define("ADMIN_AJAX_MODE", true);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

if (!check_bitrix_sessid()) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

CModule::IncludeModule('bnpl.payment');
CModule::IncludeModule('sale');

$apiHost = Config::get('BNPL_PAYMENT_API_HOST');
$partnerCode = Config::get('BNPL_PAYMENT_PARTNER_CODE');
$accountingServiceToken = Config::get('BNPL_PAYMENT_API_OAUTH_ACCOUNTING_SERVICE_TOKEN');

$transport = new GuzzleTransport();
$logger = DebugLoggerFactory::create()->createLogger();
$transport->setLogger($logger);
$api = Api::create($apiHost, new BearerTokenAuth($accountingServiceToken), $transport);
$request = Context::getCurrent()->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$orderId = $request->get('order_id');
$amount = $request->get('amount') ?: 0;
$returnItems = $request->get('returnItems') ?: false;
try {
    $order = Order::load($orderId);
    if ($amount > 0) {
        $amountAR = $order->getSumPaid() - $amount;
        if ($amountAR <= 0) {
            $amountAR = 0;
        }
    } else {
        $amountAR = 0;
    }
    $api->otp->checkOtpReturn(new CheckOtpReturn($amountAR, $partnerCode, $request->get('order_id'), $request->get('otp')));
    // обновление корзины
    if (!empty($returnItems)) {
        $returnItems = json_decode($returnItems, true);
        $basket = $order->getBasket();
        foreach ($returnItems as $returnItem) {

            if ($basketItem = $basket->getItemById($returnItem['ID'])) {
                $newQuantity = $basketItem->getQuantity() - $returnItem['quant'];
                if ($newQuantity > 0) {
                    $basketItem->setField('QUANTITY', $newQuantity);
                } else {
                    $basketItem->delete();
                }
                $basketItem->save();
            }
        }
    }
    // возврат платежей
    $bnplPaymentService = null;
    foreach ($order->getPaymentCollection() as $payment) {
        $paySystemService = $payment->getPaySystem();
        if ($paySystemService->getField('CODE') == 'factoring004') {
            $bnplPaymentService = $paySystemService;
        }
        $payment->setReturn("P");
    }
    // Добавление нового платежа на сумму после возврата
    if ($amountAR > 0) {
        $newPayment = $order->getPaymentCollection()->createItem($bnplPaymentService);
        $newPayment->setField('SUM', $amountAR);
        $newPayment->setPaid('Y');
    }
    $order->save();
    $response->setStatus(200);
    $response->setContent(json_encode(['success' => true]));
} catch (Exception $e) {
    if ($e instanceof ErrorResponseException) {
        $errorResponse = $e->getErrorResponse();
        $logger->error(sprintf(
            '%s: %s: %s',
            $errorResponse->getError(),
            $errorResponse->getMessage(),
            json_encode($errorResponse->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ));
        $error = $errorResponse->getError() . ': ' . $errorResponse->getMessage();
    } elseif ($e instanceof ValidationException) {
        $errorResponse = $e->getResponse();
        $logger->error(sprintf(
            '%s: %s',
            $errorResponse->getMessage(),
            json_encode($errorResponse->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ));
        $error = $errorResponse->getMessage();
    } else {
        $isDebug = Configuration::getValue('exception_handling')['debug'];

        $logger->error($e);
        $error = $isDebug ? $e->getMessage() : 'An error occurred. Please try again.';
    }

    $response->setStatus(500);
    $response->setContent(json_encode(['success' => false, 'error' => $error]));
}

$response->addHeader('Content-Type', 'application/json');
$response->send();
