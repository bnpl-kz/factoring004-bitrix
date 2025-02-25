<?php


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit;
}

use Bnpl\Payment\BitrixSimpleCache;
use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use Bitrix\Main\Application;
use Bitrix\Sale\PersonType;

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

$personTypes = PersonType::getList()->fetchAll();

foreach ($personTypes as $personType) {
    if ($personType['ACTIVE'] === 'Y') {
        $personTypeId = $personType['ID'];

        $apiHost = Config::get('BNPL_PAYMENT_API_HOST', $personTypeId);
        $oAuthLogin = Config::get('BNPL_PAYMENT_API_OAUTH_LOGIN', $personTypeId);
        $oAuthPassword = Config::get('BNPL_PAYMENT_API_OAUTH_PASSWORD', $personTypeId);

        $cache = new BitrixSimpleCache(Application::getInstance()->getCache());
        $transport = new GuzzleTransport();
        $logger = DebugLoggerFactory::create()->createLogger();
        $transport->setLogger($logger);

        $tokenManager = new OAuthTokenManager($apiHost . '/users/api/v1', $oAuthLogin, $oAuthPassword, $transport);
        $tokenManager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment_' . $personTypeId);

        $tokenManager->clearCache();
    }
}

$response = new \Bitrix\Main\HttpResponse();
$response->addHeader('Content-Type', 'application/json');

$response->setContent(json_encode(['success' => true]));

$response->send();