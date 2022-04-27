<?php

require_once __DIR__ . '/../lib/Config.php';

use Bitrix\Main\ArgumentNullException;
use Bitrix\Sale\Order;
use Bnpl\Payment\Config;

if (strpos($_SERVER['REQUEST_URI'], 'admin/sale_order_view.php') !== false) {
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
} elseif (strpos($_SERVER['REQUEST_URI'], 'admin/sale_order_shipment_edit.php') !== false) {
    if (empty($_GET['order_id'])) {
        return;
    }

    try {
        $__order = Order::load($_GET['order_id']);

        if ((int) $__order->getField('PAY_SYSTEM_ID') !== (int) Config::getPaySystemId()) {
            return;
        }

        if ($__order->getShipmentCollection()[0]->getField('STATUS_ID') === 'DF') {
            require_once __DIR__ . '/sale_order_shipment_edit_disable_script.php';
            return;
        }
    } catch (ArgumentNullException $e) {
        // do nothing
        return;
    }

    \Bitrix\Main\UI\Extension::load('ui.notification');
    require_once __DIR__ . '/sale_order_shipment_edit.php';
} elseif (strpos($_SERVER['REQUEST_URI'], 'admin/sale_order_payment_edit.php') !== false) {
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
} elseif (strpos($_SERVER['REQUEST_URI'], 'admin/sale_pay_system_edit.php') !== false) {
    require_once __DIR__ . '/set_values_default.php';
    require_once __DIR__ . '/change_token_fields_type.php';
}
