<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use Bitrix\Main\Application;

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
$oAuthLogin = Config::get('BNPL_PAYMENT_API_OAUTH_LOGIN');
$oAuthPassword = Config::get('BNPL_PAYMENT_API_OAUTH_PASSWORD');


$transport = new GuzzleTransport();
$logger = DebugLoggerFactory::create()->createLogger();
$transport->setLogger($logger);
$response = new \Bitrix\Main\HttpResponse();
$response->addHeader('Content-Type', 'application/json');

\Bnpl\Payment\AuthTokenManager::init($oAuthLogin, $oAuthPassword, $apiHost, $transport, Application::getInstance())->clearCache();

$response->setContent(json_encode(['success' => true]));

$response->send();

