<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bnpl\Payment\BitrixSimpleCache;
use Bnpl\Payment\Config;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
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

$consumerKey = Config::get('BNPL_PAYMENT_CONSUMER_KEY');
$consumerSecret = Config::get('BNPL_PAYMENT_CONSUMER_SECRET');
$apiHost = Config::get('BNPL_PAYMENT_API_HOST');
$partnerCode = Config::get('BNPL_PAYMENT_PARTNER_CODE');

$transport = new GuzzleTransport();
$cache = new BitrixSimpleCache(Application::getInstance()->getCache());

$tokenManager = new OAuthTokenManager(rtrim($apiHost, '/') . '/oauth2', $consumerKey, $consumerSecret, $transport);
$tokenManager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment');
$api = Api::create($apiHost, new BearerTokenAuth($tokenManager->getAccessToken()->getAccessToken()), $transport);
$request = Context::getCurrent()->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$orderId = $request->get('order_id');

try {
    $result = $api->changeStatus->changeStatusJson([
        new MerchantsOrders($partnerCode, [new ReturnOrder($orderId, ReturnStatus::RETURN(), 0)])
    ]);

    if ($result->getSuccessfulResponses()) {
        $response->setContent(json_encode(['success' => true]));
    } else {
        $responses = $result->getErrorResponses();

        foreach ($responses as $response) {
            $response->setContent(json_encode(['success' => false, 'response' => $response->toArray()]));
            break;
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
