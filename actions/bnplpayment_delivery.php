<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bitrix\Sale\Order;
use Bnpl\Payment\BitrixSimpleCache;
use Bnpl\Payment\Config;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Otp\SendOtp;
use BnplPartners\Factoring004\Transport\Transport;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

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

$psrFactory = new HttpFactory();
$transport = new Transport($psrFactory, $psrFactory, $psrFactory, new Client());
$cache = new BitrixSimpleCache(Application::getInstance()->getCache());

$tokenManager = new OAuthTokenManager($transport, rtrim($apiHost, '/') . '/oauth2', $consumerKey, $consumerSecret);
$tokenManager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment');
$api = Api::create($transport, $apiHost, new BearerTokenAuth($tokenManager->getAccessToken()->getAccessToken()));
$request = Context::getCurrent()->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$orderId = $request->get('order_id');
$ids = Config::getDeliveryIds();

try {
    if (array_intersect($ids, Order::load($orderId)->getDeliveryIdList())) {
        // should send OTP
        $api->otp->sendOtp(new SendOtp($partnerCode, $orderId));
        $response->setContent(json_encode(['otp' => true, 'success' => true]));
    } else {
        // Delivery without OTP
        $result = $api->changeStatus->changeStatusJson([
            new MerchantsOrders($partnerCode, [new DeliveryOrder($orderId, DeliveryStatus::DELIVERY())])
        ]);

        if ($result->getSuccessfulResponses()) {
            $response->setContent(json_encode(['otp' => false, 'success' => true]));
        } else {
            $response->setContent(json_encode(['otp' => false, 'success' => false]));
        }
    }

    $response->setStatus(200);
} catch (Exception $e) {
    $isDebug = Configuration::getValue('exception_handling')['debug'];

    $response->setStatus(500);
    $response->setContent(json_encode(['success' => false, 'error' => $isDebug ? (string) $e : 'An error occurred. Please try again.']));
    error_log($e);
}

$response->addHeader('Content-Type', 'application/json');
$response->send();
