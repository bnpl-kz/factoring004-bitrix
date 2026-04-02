<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Sale\Payment;
use Bnpl\Payment\PostlinkProcessor;

define('NOT_CHECK_PERMISSIONS', true);

CModule::IncludeModule('bnpl.payment');
CModule::IncludeModule('sale');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    exit;
}

$postlinkProcessor = new PostlinkProcessor();
$request = Application::getInstance()->getContext()->getRequest();
$payment = $postlinkProcessor->getPaymentFromRequest($request);
$result = $postlinkProcessor->processRequest($payment, $request);
$postlinkProcessor->sendResponse($result, $request);
