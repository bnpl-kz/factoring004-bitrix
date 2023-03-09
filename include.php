<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\Payment\EventHandler' => 'lib/events/event_handler.php',
    '\Bnpl\Payment\PushAdminScripts' => 'lib/events/PushAdminScripts.php',
);

CModule::AddAutoloadClasses('bnpl.payment', $classes);
