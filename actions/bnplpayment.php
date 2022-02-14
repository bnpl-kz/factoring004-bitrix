<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!check_bitrix_sessid()) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

use Bitrix\Main\Application;
use Bitrix\Main\Config\Configuration;
use Bnpl\Payment\BitrixSimpleCache;
use Bnpl\Payment\Config;
use Bnpl\Payment\PaymentProcessor;
use Bnpl\Payment\PreAppOrderManager;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Transport\Transport;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);

CModule::IncludeModule('bnpl.payment');
CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    exit;
}

$consumerKey = Config::get('BNPL_PAYMENT_CONSUMER_KEY');
$consumerSecret = Config::get('BNPL_PAYMENT_CONSUMER_SECRET');
$apiHost = Config::get('BNPL_PAYMENT_API_HOST');

$psrFactory = new HttpFactory();
$transport = new Transport($psrFactory, $psrFactory, $psrFactory, new Client());
$cache = new BitrixSimpleCache(Application::getInstance()->getCache());

$tokenManager = new OAuthTokenManager($transport, $apiHost, $consumerKey, $consumerSecret);
$tokenManager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment');
$api = Api::create($transport, $apiHost, new BearerTokenAuth($tokenManager->getAccessToken()->getAccessToken()));

$request = Application::getInstance()->getContext()->getRequest();
$processor = new PaymentProcessor($api, new PreAppOrderManager());

try {
    $response = $processor->preApp($request);
} catch (\Exception $e) {
    $isDebug = Configuration::getValue('exception_handling')['debug'];

    $response = new \Bitrix\Main\HttpResponse();
    $response->setStatus(500);
    $response->setContent($isDebug ? $e : 'An error occurred. Please try again.');
    error_log($e);
}

$response->send();
