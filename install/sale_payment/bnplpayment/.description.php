<?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/bnpl.payment/handler/.description.php');

/**
 * В 16 версии при подключении description в нем должен быть описан массив $arPSCorrespondence с описаниями полей который сейчас хранятся в $data['CODES']
 */
if (SM_VERSION == '16.0.11') {
    $psTitle = $data["NAME"];
    $psDescription = '';
    $arPSCorrespondence = $data['CODES'];
    foreach ($arPSCorrespondence as &$item) {
        if (isset($item['DEFAULT'])) {
            $item['VALUE'] = $item['DEFAULT']['PROVIDER_VALUE'];
        }
        if (isset($item['INPUT'])) {
            $item = array_merge($item, $item['INPUT']);
        }
    }
}