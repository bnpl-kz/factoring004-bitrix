<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use Bnpl\Payment\DeliveryManager;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Otp\SendOtp;
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

$data = json_decode($request->getInput(), true);

$orderId = $data['order_id'];

$deliveryItems = $data['items'] ?? [];

$ids = Config::getDeliveryIds();

$order = \Bitrix\Sale\Order::load($orderId);

try {

    $deliveryManager = DeliveryManager::create($order, $deliveryItems);
    $paidSum = $deliveryManager->calculateAmount();

    if (array_intersect($ids, $order->getDeliveryIdList())) {
        // should send OTP
        $api->otp->sendOtp(new SendOtp($partnerCode, $orderId, $paidSum));
        $response->setContent(json_encode(['otp' => true, 'success' => true]));
    } else {
        // Delivery without OTP
        $result = $api->changeStatus->changeStatusJson([
            new MerchantsOrders($partnerCode, [new DeliveryOrder($orderId, DeliveryStatus::DELIVERY(), $paidSum)]),
        ]);

        if ($result->getSuccessfulResponses()) {
            $response->setContent(json_encode(['otp' => false, 'success' => true]));
            $deliveryManager->updateOrder();
        } else {
            $response->setContent(json_encode(['otp' => false, 'success' => false]));
        }
    }

    $response->setStatus(200);
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
