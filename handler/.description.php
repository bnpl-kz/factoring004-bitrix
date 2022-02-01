<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

$isAvailable = true;

$data = [
    'NAME' => 'BNPLPayment',
    'SORT' => 400,
    'IS_AVAILABLE' => $isAvailable,
    'CODES' => [
        'BNPL_PAYMENT_CONSUMER_KEY' => [
            'NAME' => 'Consumer Key',
            'SORT' => 100,
            'GROUP' => 'CREDENTIALS',
        ],

        'BNPL_PAYMENT_CONSUMER_SECRET' => [
            'NAME' => 'Consumer Secret',
            'SORT' => 200,
            'GROUP' => 'CREDENTIALS',
        ],

        'BNPL_PAYMENT_API_HOST' => [
            'NAME' => 'API Host',
            'SORT' => 300,
            'GROUP' => 'CREDENTIALS',
        ],

        'BNPL_PAYMENT_PARTNER_NAME' => [
            'NAME' => 'Partner Name',
            'SORT' => 400,
            'GROUP' => 'MERCHANT PARAMETERS',
        ],

        'BNPL_PAYMENT_PARTNER_CODE' => [
            'NAME' => 'Partner Code',
            'SORT' => 500,
            'GROUP' => 'MERCHANT PARAMETERS',
        ],

        'BNPL_PAYMENT_POINT_CODE' => [
            'NAME' => 'Point Code',
            'SORT' => 600,
            'GROUP' => 'MERCHANT PARAMETERS',
        ],

        'BNPL_PAYMENT_POST_LINK_URL' => [
            'NAME' => 'Post Link URL',
            'SORT' => 700,
            'GROUP' => 'ORDER PARAMETERS',
        ],

        'BNPL_PAYMENT_SUCCESS_REDIRECT_URL' => [
            'NAME' => 'Success Redirect URL',
            'SORT' => 800,
            'GROUP' => 'ORDER PARAMETERS',
        ],

        'BNPL_PAYMENT_FAIL_REDIRECT' => [
            'NAME' => 'Fail Redirect URL',
            'SORT' => 900,
            'GROUP' => 'ORDER PARAMETERS',
        ],

        'PAYMENT_ID' => [
            'NAME' => 'Номер оплаты',
            'SORT' => 1000,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'ID',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],

        'PAYMENT_CURRENCY' => [
            'NAME' => 'Валюта счета',
            'SORT' => 2000,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'CURRENCY',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],

        'PAYMENT_SHOULD_PAY' => [
            'NAME' => 'К оплате',
            'SORT' => 3000,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'SUM',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],
    ],
];
