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
        'PAYMENT_ID' => [
            'NAME' => 'Номер оплаты',
            'SORT' => 500,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'ID',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],
        'PAYMENT_CURRENCY' => [
            'NAME' => 'Валюта счета',
            'SORT' => 600,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'CURRENCY',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],
        'PAYMENT_SHOULD_PAY' => [
            'NAME' => 'К оплате',
            'SORT' => 700,
            'GROUP' => 'PAYMENT',
            'DEFAULT' => [
                'PROVIDER_VALUE' => 'SUM',
                'PROVIDER_KEY' => 'PAYMENT'
            ]
        ],
    ],
];
