<?php

namespace Bnpl\Payment;

use Bitrix\Main\Application;
use CDatabase;

/**
 * Class PreAppOrderManager
 * @package Bnpl\Payment
 */
final class PreAppOrderManager {

    /**
     * @var \Bitrix\Main\Data\Connection|\Bitrix\Main\DB\Connection|null
     */
    private $connection;

    public function __construct()
    {
        $this->connection = Application::getInstance()->getConnectionPool()->getConnection();
    }

    public function createOrder($bitrixOrderId, $preappId)
    {
        try {
            $this->connection->startTransaction();
            $orderResult = OrdersTable::add(array(
                'ORDER_ID' => $bitrixOrderId,
            ));
            PreappsTable::add(array(
                'PREAPP_UID'=>$preappId,
                'ORDER_ID'=>$orderResult->getId()
            ));
            $this->connection->commitTransaction();
        } catch (\Exception $e) {
            $this->connection->rollbackTransaction();
            throw new PreAppOrderManagerException('Cannot insert data in DB', 0,$e);
        }

    }

    public function updateOrder($bitrixOrderId, $status)
    {
        try {
            OrdersTable::update($bitrixOrderId,array(
                'STATUS' => $status,
            ));
        } catch (\Exception $e) {
            throw new PreAppOrderManagerException('Cannot update data in DB', 0,$e);
        }
    }

    public function syncOrder($bitrixOrderId, $preappId)
    {
        try {
            if ($orderId = $this->getOrderId($bitrixOrderId)) {
               PreappsTable::add([
                   'PREAPP_UID'=>$preappId,
                   'ORDER_ID'=>$orderId
               ]);
            } else {
                $this->createOrder($bitrixOrderId, $preappId);
            }
        } catch (\Exception $e) {
            throw new PreAppOrderManagerException('Sync error',0, $e);
        }
    }


    private function getOrderId($bitrixOrderId)
    {
        return OrdersTable::getRow(
            [
                'filter'=>array('ORDER_ID'=>$bitrixOrderId)
            ])['ID'];
    }
}
