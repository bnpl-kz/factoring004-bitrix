<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    exit;
}

global $DB;
$delivery = array();
$dbOption = $DB->Query("SELECT ID, NAME FROM b_sale_delivery_srv");
while ($row = $dbOption->Fetch())
{
    $delivery[$row['ID']] = $row['NAME'];
}


$isAvailable = true;

$data = array(
    'NAME' => 'Рассрочка 0-0-4',
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

        'BNPL_PAYMENT_API_OAUTH_TOKEN_PATH' => array(
            'NAME' => 'OAuth Token Path',
            'SORT' => 350,
            'GROUP' => 'CREDENTIALS',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => '/',
                'PROVIDER_KEY' => 'VALUE',
            ),
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

        'BNPL_PAYMENT_SUCCESS_REDIRECT_URL' => array(
            'NAME' => 'Success Redirect URL',
            'SORT' => 900,
            'GROUP' => 'ORDER PARAMETERS',
        ),

        'BNPL_PAYMENT_FAIL_REDIRECT' => array(
            'NAME' => 'Fail Redirect URL',
            'SORT' => 1000,
            'GROUP' => 'ORDER PARAMETERS'
        ),

        'BNPL_PAYMENT_POST_LINK' => array(
            'NAME' => 'Post Link',
            'SORT' => 1100,
            'GROUP' => 'ORDER PARAMETERS',
            'DEFAULT' => array(
                'PROVIDER_VALUE' => '/bitrix/tools/sale_ps_result.php?ps=bnpl.payment',
                'PROVIDER_KEY' => 'VALUE',
            ),
        ),

        'BNPL_PAYMENT_FILE' => array(
            'NAME' => 'Загрузите файл оферты',
            'SORT' => 1200,
            'GROUP' => 'ORDER PARAMETERS',
            'INPUT'   => array(
                'TYPE' => 'FILE',
            ),
        ),

    )
);

foreach ($delivery as $key => $item) {
    $data['CODES']['BNPL_PAYMENT_DELIVERY_'.$key] = array(
            'NAME' => $item,
            'SORT' => 1300,
            'GROUP' => 'DELIVERY PARAMETERS',
            'INPUT' => array(
                'TYPE' => 'ENUM',
                'OPTIONS' => array(
                    'N'=>'Нет',
                    'Y'=>'Да'
                ),
            ),
        );
}

?>
