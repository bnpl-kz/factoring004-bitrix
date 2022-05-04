<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/bx_root.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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
use BnplPartners\Factoring004\Transport\GuzzleTransport;

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);

CModule::IncludeModule('bnpl.payment');
CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    exit;
}

$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : false;

if (!$order_id) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}

$arOrder = CSaleOrder::GetByID($order_id);

$paySystemID = $arOrder['PAY_SYSTEM_ID'];

$paySystemAction = CSalePaySystemAction::GetList(
    [],
    array('%ACTION_FILE' => 'bnplpayment'),
    false,
    false,
    ['ID', 'ACTION_FILE', 'PARAMS']
);

$action = $paySystemAction->Fetch();

$params = unserialize($action['PARAMS']);

$apiHost = $params['BNPL_PAYMENT_API_HOST']['VALUE'];
$preAppToken = $params['BNPL_PAYMENT_API_OAUTH_PREAPP_TOKEN']['VALUE'];

$api = Api::create($apiHost, new BearerTokenAuth($preAppToken));

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
