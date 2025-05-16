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
use Bitrix\Main\Engine\Response\Json;
use Bnpl\PaymentPad\Config;
use Bnpl\PaymentPad\DebugLoggerFactory;
use Bnpl\PaymentPad\PaymentProcessor;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Transport\GuzzleTransport;

define("STOP_STATISTICS", true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define("DisableEventsCheck", true);

CModule::IncludeModule('bnpl.pad');
CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    exit;
}

$apiHost = Config::get('BNPL_PAYMENT_PAD_API_HOST');
$oAuthLogin = Config::get('BNPL_PAYMENT_PAD_API_OAUTH_LOGIN');
$oAuthPassword = Config::get('BNPL_PAYMENT_PAD_API_OAUTH_PASSWORD');
$debug = Config::get('BNPL_PAYMENT_PAD_DEBUG');

$transport = new GuzzleTransport();
$logger = DebugLoggerFactory::create()->createLogger();
$transport->setLogger($logger);

$token = \Bnpl\PaymentPad\AuthTokenManager::init($oAuthLogin, $oAuthPassword, $apiHost, $transport, Application::getInstance())->getToken();

$api = Api::create($apiHost, new BearerTokenAuth($token), $transport);

$request = Application::getInstance()->getContext()->getRequest();
$processor = new PaymentProcessor($api);

$session = \Bitrix\Main\Application::getInstance()->getSession();

try {
    $response = $processor->preApp($request);
} catch (\Exception $e) {

    if ($debug === 'on') {
        $session->set('bnplpad_debug', $e);
    }

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

    if (Config::get('BNPL_PAYMENT_PAD_CLIENT_ROUTE') === 'modal') {
        $response = (new Json([
            'redirectErrorPage' => '/personal/order/payment/bnplpad_error.php'
        ]));
    } else {
        $response = new \Bitrix\Main\Engine\Response\Redirect('/personal/order/payment/bnplpad_error.php');
    }
}

$response->send();
