<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}


$isAvailable = true;

$data = array(
    'NAME' => 'BNPLPayment',
    'SORT' => 400,
    'IS_AVAILABLE' => $isAvailable,
    'CODES' => array(
        'BNPL_PAYMENT_CONSUMER_KEY' => array(
            'NAME' => 'Consumer Key',
            'SORT' => 100,
            'GROUP' => 'CREDENTIALS',
        ),

        'BNPL_PAYMENT_CONSUMER_SECRET' => array(
            'NAME' => 'Consumer Secret',
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

        'BNPL_PAYMENT_SUCCESS_REDIRECT_URL' => array(
            'NAME' => 'Success Redirect URL',
            'SORT' => 800,
            'GROUP' => 'ORDER PARAMETERS',
        ),

        'BNPL_PAYMENT_FAIL_REDIRECT' => array(
            'NAME' => 'Fail Redirect URL',
            'SORT' => 900,
            'GROUP' => 'ORDER PARAMETERS'
        ),
    )
);
