<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use CAdminTabControl;

class PushAdminScripts
{
    private const PATHS_MAP = [
        '/bitrix/admin/sale_pay_system_edit.php' => [
            __DIR__ . '/../../template/set_values_default.php',
            __DIR__ . '/../../template/clear_cache_button.php',
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
