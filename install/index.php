<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\IO\File;
use Bnpl\Payment\PaymentScheduleAsset;

require_once __DIR__ . '/../vendor/autoload.php';

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
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment_delivery.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpayment_delivery.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment_delivery_check_otp.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpayment_delivery_check_otp.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment_return.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpayment_return.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions/bnplpayment_return_check_otp.php',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/bnplpayment_return_check_otp.php',
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/schedule/' . PaymentScheduleAsset::FILE_CSS,
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/factoring004/' . PaymentScheduleAsset::FILE_CSS,
            true,
            true
        );

        CopyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/schedule/' . PaymentScheduleAsset::FILE_JS,
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/factoring004/' . PaymentScheduleAsset::FILE_JS,
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
       DeleteDirFilesEx('/bitrix/admin/bnplpayment_delivery.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpayment_delivery_check_otp.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpayment_return.php');
       DeleteDirFilesEx('/bitrix/admin/bnplpayment_return_check_otp.php');
       DeleteDirFilesEx('/bitrix/images/sale/sale_payments/bnplpayment.png');
       DeleteDirFilesEx('/bitrix/css/factoring004/' . PaymentScheduleAsset::FILE_CSS);
       DeleteDirFilesEx('/bitrix/js/factoring004/' . PaymentScheduleAsset::FILE_JS);
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
