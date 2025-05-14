<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\PaymentPad\EventHandler' => 'lib/events/event_handler.php',
    '\Bnpl\PaymentPad\PushAdminScripts' => 'lib/events/PushAdminScripts.php',
);

CModule::AddAutoloadClasses('bnpl.pad', $classes);
