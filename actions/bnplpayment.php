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
use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use Bnpl\Payment\PaymentProcessor;
use Bnpl\Payment\PreAppOrderManager;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
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

$apiHost = Config::get('BNPL_PAYMENT_API_HOST');
$preAppToken = Config::get('BNPL_PAYMENT_API_OAUTH_PREAPP_TOKEN');

$transport = new GuzzleTransport();
$logger = DebugLoggerFactory::create()->createLogger();
$transport->setLogger($logger);
$api = Api::create($apiHost, new BearerTokenAuth($preAppToken), $transport);

$request = Application::getInstance()->getContext()->getRequest();
$processor = new PaymentProcessor($api, new PreAppOrderManager());

try {
    $response = $processor->preApp($request);
} catch (\Exception $e) {
    if ($e instanceof ErrorResponseException) {
        $response = $e->getErrorResponse();
        $logger->error(sprintf(
            '%s: %s: %s',
            $response->getError(),
            $response->getMessage(),
            json_encode($response->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ));
        $error = $response->getError() . ': ' . $response->getMessage();
    } elseif ($e instanceof ValidationException) {
        $response = $e->getResponse();
        $logger->error(sprintf(
            '%s: %s',
            $response->getMessage(),
            json_encode($response->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        ));
        $error = $response->getMessage();
    } else {
        $isDebug = Configuration::getValue('exception_handling')['debug'];

        $logger->error($e);
        $error = $isDebug ? $e->getMessage() : 'An error occurred. Please try again.';
    }

    $response = new \Bitrix\Main\HttpResponse();
    $response->setStatus(500);
    $response->setContent($error);
}

$response->send();
