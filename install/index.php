<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;

require_once __DIR__ . '/../vendor/autoload.php';

class bnpl_pad extends CModule
{
    var $MODULE_ID = 'bnpl.pad';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;

    /**
     * bnpl_pad constructor.
     */
    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . '/version.php');

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage('BNPL_PAYMENT_PAD_INSTALL_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('BNPL_PAYMENT_PAD_INSTALL_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('BNPL_PAYMENT_PAD_DEVELOPMENT_TEAM');
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
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad.php',
            $_SERVER['DOCUMENT_ROOT'] . '/personal/order/payment/bnplpad.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_cache_clear.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpad_cache_clear.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_delivery.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpad_delivery.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_delivery_check_otp.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpad_delivery_check_otp.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_return.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpad_return.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_return_check_otp.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpad_return_check_otp.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpad_error.php',
            $_SERVER['DOCUMENT_ROOT'] . '/personal/order/payment/bnplpad_error.php',
            true,
            true
        );

        mkdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/tmp/factoring004_pad', 0755, true);

        $source = Application::getDocumentRoot() . '/bitrix/modules/' . $this->MODULE_ID . '/install';
        $logo_dir = Application::getDocumentRoot() . '/bitrix/images/sale/sale_payments/';
        //File::putFileContents($logo_dir . 'bnplpayment_pad.png', File::getFileContents($source . "/sale_payment/bnplpad/bnplpayment.png"));
    }

    public function installEvents() {
        EventManager::getInstance()->registerEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\PaymentPad\EventHandler',
            'hidePaySystem'
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\Bnpl\PaymentPad\PadPushAdminScripts',
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
       DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/bnplpad');
       DeleteDirFilesEx('/personal/order/payment/bnplpad.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpad_delivery.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpad_cache_clear.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpad_delivery_check_otp.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpad_return.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpad_return_check_otp.php');
       //DeleteDirFilesEx('/bitrix/images/sale/sale_payments/bnplpayment_pad.png');
       DeleteDirFilesEx('/bitrix/tmp/_pad');
       DeleteDirFilesEx('/personal/order/payment/bnplpad_error.php');
    }

    public function uninstallEvents() {
        EventManager::getInstance()->unRegisterEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\PaymentPad\EventHandler',
            'hidePaySystem'
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnAdminTabControlBegin',
            $this->MODULE_ID,
            '\Bnpl\PaymentPad\PadPushAdminScripts',
            'push'
        );
    }
}
