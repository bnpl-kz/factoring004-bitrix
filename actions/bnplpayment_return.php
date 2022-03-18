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
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\Otp\SendOtpReturn;

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

$consumerKey = Config::get('BNPL_PAYMENT_CONSUMER_KEY');
$consumerSecret = Config::get('BNPL_PAYMENT_CONSUMER_SECRET');
$apiHost = Config::get('BNPL_PAYMENT_API_HOST');
$partnerCode = Config::get('BNPL_PAYMENT_PARTNER_CODE');
$accountingServiceToken = Config::get('BNPL_PAYMENT_API_OAUTH_ACCOUNTING_SERVICE_TOKEN');

$api = Api::create($apiHost, new BearerTokenAuth($accountingServiceToken));
$request = Context::getCurrent()->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$orderId = $request->get('order_id');
$ids = Config::getDeliveryIds();

try {
    if (array_intersect($ids, Order::load($orderId)->getDeliveryIdList())) {
        // should send OTP
        $api->otp->sendOtpReturn(new SendOtpReturn(0, $partnerCode, $orderId));
        $response->setContent(json_encode(['otp' => true, 'success' => true]));
    } else {
        // Delivery without OTP
        $result = $api->changeStatus->changeStatusJson([
            new MerchantsOrders($partnerCode, [new ReturnOrder($orderId, ReturnStatus::RETURN(), 0)])
        ]);

        if ($result->getSuccessfulResponses()) {
            $response->setContent(json_encode(['success' => true]));
        } else {
            $responses = $result->getErrorResponses();

            foreach ($responses as $res) {
                $response->setContent(json_encode(['success' => false, 'response' => $res->toArray()]));
                break;
            }
        }
    }

    $response->setStatus(200);
} catch (Exception $e) {
    $isDebug = Configuration::getValue('exception_handling')['debug'];

    $response->setStatus(500);
    $response->setContent(json_encode(['success' => false, 'error' => 'An error occurred. Please try again.']));
    error_log($e);
}

$response->addHeader('Content-Type', 'application/json');
$response->send();
