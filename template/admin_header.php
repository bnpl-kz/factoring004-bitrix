<?php

require_once __DIR__ . '/../lib/Config.php';

use Bitrix\Main\ArgumentNullException;
use Bitrix\Sale\Order;
use Bnpl\Payment\Config;

if (strpos($_SERVER['REQUEST_URI'], 'admin/sale_order_payment_edit.php') !== false) {
    try {
        $__order = Order::load($_GET['order_id']);

        if ((int) $__order->getField('PAY_SYSTEM_ID') !== (int) Config::getPaySystemId()) {
            return;
        }

        if ($__order->isPaid()) {
            \Bitrix\Main\UI\Extension::load('ui.notification');
            require_once __DIR__ . '/disable_payment_status.php';
            require_once __DIR__ . '/form_return_payment.php';
        }
    } catch (ArgumentNullException $e) {
        // do nothing
        return;
    }
}
