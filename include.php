<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\Payment\PreAppOrderManager' => 'lib/facades/preapp_order_manager.php',
    '\Bnpl\Payment\OrdersTable' => 'lib/orm/orders.php',
    '\Bnpl\Payment\PreappsTable' => 'lib/orm/preapps.php',
    '\Bnpl\Payment\PreAppOrderManagerException' => 'lib/exceptions/preapp_order_manager_exception.php',
    '\Bnpl\Payment\EventHandler' => 'lib/events/event_handler.php',
);

CModule::AddAutoloadClasses('bnpl.payment', $classes);
