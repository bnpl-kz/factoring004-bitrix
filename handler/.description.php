<?php

use Bitrix\Main\Localization\Loc;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}


$isAvailable = true;

$data = array(
    'NAME' => Loc::getMessage('BNPL_PAYMENT_NAME'),
    'IS_AVAILABLE' => $isAvailable,
    'CODES' => array(
        'BNPL_PAYMENT_API_OAUTH_PREAPP_TOKEN' => array(
            'NAME' => 'OAuth Preapp Token',
            'SORT' => 100,
            'GROUP' => 'CREDENTIALS',
        ),

        'BNPL_PAYMENT_API_OAUTH_ACCOUNTING_SERVICE_TOKEN' => array(
            'NAME' => 'OAuth Accounting Service Token',
            'SORT' => 200,
            'GROUP' => 'CREDENTIALS',
        ),

        'BNPL_PAYMENT_API_HOST' => array(
            'NAME' => 'API Host',
            'SORT' => 300,
            'GROUP' => 'CREDENTIALS',
        ),

        'BNPL_PAYMENT_PARTNER_NAME' => array(
            'NAME' => 'Partner Name',
            'SORT' => 400,
            'GROUP' => 'MERCHANT PARAMETERS',
        ),

        'BNPL_PAYMENT_PARTNER_CODE' => array(
            'NAME' => 'Partner Code',
            'SORT' => 500,
            'GROUP' => 'MERCHANT PARAMETERS',
        ),

        'BNPL_PAYMENT_POINT_CODE' => array(
            'NAME' => 'Point Code',
            'SORT' => 600,
            'GROUP' => 'MERCHANT PARAMETERS',
        ),

        'BNPL_PAYMENT_PARTNER_EMAIL' => array(
            'NAME' => 'Partner Email',
            'SORT' => 700,
            'GROUP' => 'MERCHANT PARAMETERS',
        ),

        'BNPL_PAYMENT_PARTNER_WEBSITE' => array(
            'NAME' => 'Partner Website',
            'SORT' => 800,
            'GROUP' => 'MERCHANT PARAMETERS',
        ),

        'BNPL_PAYMENT_CLIENT_ROUTE' => array(
            'NAME' => Loc::getMessage('BNPL_PAYMENT_CLIENT_ROUTE'),
            'SORT' => 1250,
            'GROUP' => 'CLIENT PARAMETERS',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'redirect' => Loc::getMessage('BNPL_PAYMENT_CLIENT_ROUTE_REDIRECT'),
                    'modal' => Loc::getMessage('BNPL_PAYMENT_CLIENT_ROUTE_MODAL')
                ),
            ),
            'DEFAULT' => array(
                'PROVIDER_KEY' => 'INPUT',
                'PROVIDER_VALUE' => 'modal'
            )
        ),
    )
);

?>
