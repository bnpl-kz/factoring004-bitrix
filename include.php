<?php

require_once __DIR__ . '/vendor/autoload.php';

$classes = array(
    '\Bnpl\Payment\EventHandler' => 'lib/events/event_handler.php',
);

CModule::AddAutoloadClasses('bnpl.payment', $classes);
