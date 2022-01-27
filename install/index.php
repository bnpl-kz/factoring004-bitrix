<?php

IncludeModuleLangFile(__FILE__);

class bnpl_payment extends CModule
{
    public const MODULE_ID = 'bnpl.payment';

    public $MODULE_ID = self::MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME = 'BNPLPayment';
    public $MODULE_DESCRIPTION = 'BNPL Payment Module';
    public $PARTNER_NAME = 'BNPLPayment';
    public $PARTNER_URI = 'http://example.com';

    public function __construct()
    {
        require __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion["VERSION"] ?? null;
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"] ?? null;
    }

    /**
     * @throws Exception
     */
    public function DoInstall()
    {
        if (IsModuleInstalled('sale')) {
            $this->InstallFiles();
            RegisterModule($this->MODULE_ID);
            $message = 'Success';
        } else {
            $message = 'Error';
        }

        CAdminNotify::Add([
            'MODULE_ID' => $this->MODULE_ID,
            'TAG' => 'TEST',
            'MESSAGE' => $message,
        ]);
    }

    /**
     * @throws Exception
     */
    public function InstallFiles()
    {
        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/sale_payment',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment',
            true,
            true
        );
    }

    public function DoUninstall()
    {
        COption::RemoveOption($this->MODULE_ID);
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
    }

    public function UnInstallFiles()
    {
        return DeleteDirFilesEx(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/' . $this->MODULE_ID,
        );
    }
}