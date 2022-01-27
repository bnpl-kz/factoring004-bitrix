<?php

use Bitrix\Main\Localization\Loc;

class bnpl_payment extends CModule
{
    public $MODULE_ID = 'bnpl.payment';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;

    /**
     * bnpl_payment constructor.
     */
    public function __construct()
    {
        $arModuleVersion = array();
        require __DIR__ . '/version.php';

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage('BNPL_PAYMENT_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BNPL_PAYMENT_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('BNPL_PAYMENT_DEVELOPMENT_TEAM');
    }


    public function DoInstall()
    {
        $this->InstallFiles();
        RegisterModule($this->MODULE_ID);
        return true;
    }


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
        UnRegisterModule($this->MODULE_ID);
        $this->UnInstallFiles();
        return true;
    }

    public function UnInstallFiles()
    {
        return DeleteDirFilesEx(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/include/sale_payment/' . $this->MODULE_ID
        );
    }
}
