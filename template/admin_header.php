<?php

if (strpos($_SERVER['REQUEST_URI'], 'admin/sale_pay_system_edit.php') !== false) {
    require_once __DIR__ . '/set_values_default.php';
    require_once __DIR__ . '/change_token_fields_type.php';
}
