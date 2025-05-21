<?php

declare(strict_types=1);

namespace Bnpl\PaymentPad;

use CAdminTabControl;

class PadPushAdminScripts
{
    private const PATHS_MAP = [
        '/bitrix/admin/sale_pay_system_edit.php' => [
            __DIR__ . '/../../template/set_values_default_pad.php',
            __DIR__ . '/../../template/clear_cache_button_pad.php',
        ],
        '/bitrix/admin/sale_order_view.php' => [
            __DIR__ . '/../../template/pre_sale_order_view.php',
        ],
        '/bitrix/admin/sale_order_shipment_edit.php' => [
            __DIR__ . '/../../template/pre_sale_order_shipment_edit.php',
        ],
        '/bitrix/admin/sale_order_payment_edit.php' => [
            __DIR__ . '/../../template/pre_sale_order_payment_edit.php',
        ],
    ];

    public static function push(CAdminTabControl $form): void
    {
        foreach (static::PATHS_MAP as $path => $files) {
            if ($GLOBALS['APPLICATION']->GetCurPage() === $path) {
                foreach ($files as $file) {
                    require_once $file;
                }
            }
        }
    }
}
