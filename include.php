<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\Payment\PreAppOrderManager' => 'lib/facades/preapp_order_manager.php',
    '\Bnpl\Payment\PreAppOrderManagerException' => 'lib/exceptions/preapp_order_manager_exception.php',
    '\Bnpl\Payment\EventHandler' => 'lib/events/event_handler.php',
);

CModule::AddAutoloadClasses('bnpl.payment', $classes);
