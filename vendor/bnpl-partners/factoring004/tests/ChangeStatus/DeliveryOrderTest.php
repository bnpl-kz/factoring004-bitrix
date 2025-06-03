<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class DeliveryOrderTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $actual = DeliveryOrder::createFromArray([
            'orderId' => '1',
            'status' => DeliveryStatus::DELIVERY()->getValue(),
            'amount' => 6000
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetStatus()
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals(DeliveryStatus::DELIVERY(), $order->getStatus());
    }

    /**
     * @return void
     */
    public function testGetOrderId()
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals('1', $order->getOrderId());

        $order = new DeliveryOrder('100', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals('100', $order->getOrderId());
    }

    /**
     * @return void
     */
    public function getTestAmount()
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals(6000, $order->getAmount());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $expected = [
            'orderId' => '1',
            'status' => DeliveryStatus::DELIVERY()->getValue(),
            'amount' => 6000
        ];

        $this->assertEquals($expected, $order->toArray());
    }
}

