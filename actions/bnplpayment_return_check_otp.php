<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bitrix\Main\Config\Configuration;
use Bitrix\Main\Context;
use Bnpl\Payment\Config;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Otp\CheckOtpReturn;

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

$api = Api::create($apiHost, new BearerTokenAuth($accountingServiceToken));
$request = Context::getCurrent()->getRequest();
$response = new \Bitrix\Main\HttpResponse();
$orderId = $request->get('order_id');

try {
    $api->otp->checkOtpReturn(new CheckOtpReturn(0, $partnerCode, $request->get('order_id'), $request->get('otp')));
    $response->setStatus(200);
    $response->setContent(json_encode(['success' => true]));
} catch (Exception $e) {
    $isDebug = Configuration::getValue('exception_handling')['debug'];
    $error = $e instanceof ValidationException
        ? json_encode($e->getResponse(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        : $e;

    $response->setStatus(500);
    $response->setContent(json_encode(['success' => false, 'error' => $isDebug ? $error : 'An error occurred. Please try again.']));
    error_log($error);
}

$response->addHeader('Content-Type', 'application/json');
$response->send();
