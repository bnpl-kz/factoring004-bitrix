<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Entity\Base;

class bnpl_payment extends CModule
{
    public $MODULE_ID = 'bnpl.payment';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;

    private $ORM_ENTITY = array(\Bnpl\Payment\OrdersTable::class, \Bnpl\Payment\PreappsTable::class);

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
    }


    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);
        $this->InstallDB();
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
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . $this->MODULE_ID . '/install/actions',
            $_SERVER['DOCUMENT_ROOT'] . '/personal/order/payment/',
            true,
            true
        );
    }

    public function InstallDB()
    {
        if (self::IncludeModule($this->MODULE_ID)) {
            foreach ($this->ORM_ENTITY as $entity) {
                if (!Application::getConnection()->isTableExists(Base::getInstance($entity)->getDBTableName())) {
                    Base::getInstance($entity)->createDbTable();
                }
            }
        }
    }

    public function installEvents() {
        EventManager::getInstance()->registerEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\Payment\EventHandler',
            'hidePaySystem'
        );
    }
//OnSaleComponentOrderCreated
    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnInstallFiles();
        $this->uninstallEvents();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    public function UnInstallFiles()
    {
       DeleteDirFilesEx('/bitrix/php_interface/include/sale_payment/bnplpayment');
       DeleteDirFilesEx('/personal/order/payment/bnplpayment.php');
    }

    public function UnInstallDB()
    {
        if (self::IncludeModule($this->MODULE_ID)) {
            foreach ($this->ORM_ENTITY as $entity) {
                if (Application::getConnection()->isTableExists(Base::getInstance($entity)->getDBTableName())) {
                    Application::getConnection()
                        ->dropTable(Base::getInstance($entity)->getDBTableName());
                }
            }
        }
    }

    public function uninstallEvents() {
        EventManager::getInstance()->unRegisterEventHandler(
            'sale',
            'OnSaleComponentOrderCreated',
            $this->MODULE_ID,
            '\Bnpl\Payment\EventHandler',
            'hidePaySystem'
        );
    }
}
