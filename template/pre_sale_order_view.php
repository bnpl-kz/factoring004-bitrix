<?php

use Bitrix\Main\ArgumentNullException;
use Bitrix\Sale\Order;
use Bnpl\Payment\Config;

if (empty($_GET['ID'])) {
    return;
}

try {
    $__order = Order::load($_GET['ID']);

    if ((int) $__order->getField('PAY_SYSTEM_ID') !== (int) Config::getPaySystemId()) {
        return;
    }

    if ($__order->getShipmentCollection()[0]->getField('STATUS_ID') === 'DF') {
        require_once __DIR__ . '/sale_order_view_disable_script.php';
    } else {
        \Bitrix\Main\UI\Extension::load('ui.notification');
        require_once __DIR__ . '/sale_order_view_script.php';
    }

    if ($__order->isPaid()) {
        require_once __DIR__ . '/disable_payment_status.php';
        require_once __DIR__ . '/return_payment.php';
    }
} catch (ArgumentNullException $e) {
    // do nothing
}