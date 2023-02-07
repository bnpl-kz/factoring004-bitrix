<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bitrix\Sale\Order;
use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use Bnpl\Payment\PartialRefundManager;
use Bnpl\Payment\PartialRefundManagerException;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Otp\SendOtpReturn;
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
$context = Context::getCurrent();
$request = $context->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$connection = $context->getApplication()->getConnectionPool()->getConnection();

if (strpos($request->getHeader('Content-Type'), 'json') !== false) {
    $data = json_decode($request->getInput(), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        $response->setStatus(400);
        $response->send();
        exit;
    }

    $orderId = $data['order_id'] ?? null;
    $amount = $data['amount'] ?? 0;
    $returnItems = $data['returnItems'] ?? [];
} else {
    $orderId = $request->get('order_id');
    $amount = $request->get('amount') ?? 0;
    $returnItems = $request->get('returnItems') ?? [];
}

$ids = Config::getDeliveryIds();
$order = Order::load($orderId);
$deliveryStatus = $order->getShipmentCollection()[0]->getField('STATUS_ID');

try {
    if ($deliveryStatus !== 'DF') {
        // cancel order
        $result = $api->changeStatus->changeStatusJson([
            new MerchantsOrders($partnerCode, [
                new CancelOrder($request->get('order_id'), CancelStatus::CANCEL()),
            ]),
        ]);

        if ($result->getSuccessfulResponses()) {
            $response->setContent(json_encode(['success' => true, 'cancel' => true]));
        } else {
            $responses = $result->getErrorResponses();

            foreach ($responses as $res) {
                $response->setContent(json_encode(['success' => false, 'response' => $res->toArray()]));
                break;
            }
        }
    } elseif (array_intersect($ids, $order->getDeliveryIdList())) {
        // should send OTP
        if ($returnItems) {
            $amountAR = PartialRefundManager::create($order, $returnItems)->calculateAmount();
        } else {
            $amountAR = $amount > 0 ? (int) ceil($order->getSumPaid() - $amount) : 0;
        }

        $api->otp->sendOtpReturn(new SendOtpReturn($amountAR, $partnerCode, $orderId));
        $response->setContent(json_encode(['otp' => true, 'success' => true]));
    } else {
        // Delivery without OTP
        $amountAR = $amount > 0 ? (int) ceil($amountAR = $order->getSumPaid() - $amount) : 0;

        if ($returnItems) {
            $connection->startTransaction();
            $manager = PartialRefundManager::create($order, $returnItems);
            $amountAR = $manager->calculateAmount();

            $manager->refund();
        }

        $result = $api->changeStatus->changeStatusJson([
            new MerchantsOrders($partnerCode, [
                new ReturnOrder(
                    $orderId, $amountAR > 0 ? ReturnStatus::PARTRETURN() : ReturnStatus::RETURN(), $amountAR
                ),
            ]),
        ]);

        if ($result->getSuccessfulResponses()) {
            $connection->commitTransaction();
            $response->setContent(json_encode(['success' => true]));
        } else {
            $connection->rollbackTransaction();
            $responses = $result->getErrorResponses();

            foreach ($responses as $res) {
                $response->setContent(json_encode(['success' => false, 'response' => $res->toArray()]));
                break;
            }
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
    } elseif ($e instanceof PartialRefundManagerException) {
        $logger->error($e);
        $error = $e->getMessage();
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
