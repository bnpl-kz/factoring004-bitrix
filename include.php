<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\PaymentPad\EventHandler' => 'lib/events/event_handler_pad.php',
    '\Bnpl\PaymentPad\PadPushAdminScripts' => 'lib/events/PadPushAdminScripts.php',
);

CModule::AddAutoloadClasses('bnpl.pad', $classes);
