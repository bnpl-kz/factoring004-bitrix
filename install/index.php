<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;

class bnpl_payment extends CModule
{
    var $MODULE_ID = 'bnpl.payment';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    /**
     * bnpl_payment constructor.
     */
    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage('BNPL_PAYMENT_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BNPL_PAYMENT_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('BNPL_PAYMENT_DEVELOPMENT_TEAM');
        $this->PARTNER_URI = "https://alfabank.kz/";
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallFiles();
        $this->installEvents();
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

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment.php',
            $_SERVER['DOCUMENT_ROOT'] . '/personal/order/payment/bnplpayment.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/template/admin_header.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/php_interface/admin_header.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment_error.php',
            $_SERVER['DOCUMENT_ROOT'] . '/personal/order/payment/bnplpayment_error.php',
            true,
            true
        );

        mkdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/factoring004', 0755, true);

        $source = Application::getDocumentRoot() . '/bitrix/modules/' . $this->MODULE_ID . '/install';
        $logo_dir = Application::getDocumentRoot() . '/bitrix/images/sale/sale_payments/';
        File::putFileContents($logo_dir . 'bnplpayment.png', File::getFileContents($source . "/sale_payment/bnplpayment/bnplpayment.png"));
    }

    public function installEvents() {
        EventManager::getInstance()->registerEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\Payment\EventHandler',
            'hidePaySystem'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\Bnpl\Payment\PushAdminScripts',
            'push'
        );
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->uninstallEvents();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function UnInstallFiles()
    {
       DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/bnplpayment');
       DeleteDirFilesEx('/personal/order/payment/bnplpayment.php');
       DeleteDirFilesEx('/bitrix/php_interface/admin_header.php');
       DeleteDirFilesEx('/bitrix/images/sale/sale_payments/bnplpayment.png');
       DeleteDirFilesEx('/bitrix/tmp/factoring004');
       DeleteDirFilesEx('/personal/order/payment/bnplpayment_error.php');
    }

    public function uninstallEvents() {
        EventManager::getInstance()->unRegisterEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\Payment\EventHandler',
            'hidePaySystem'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\Bnpl\Payment\PushAdminScripts',
            'push'
        );
    }
}
