<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class ReturnOrderTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new ReturnOrder('1', ReturnStatus::RE_TURN(), 6000);
        $actual = ReturnOrder::createFromArray([
            'orderId' => '1',
            'status' => ReturnStatus::RE_TURN()->getValue(),
            'amount' => 6000,
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetStatus()
    {
        $order = new ReturnOrder('1', ReturnStatus::RE_TURN(), 6000);
        $this->assertEquals(ReturnStatus::RE_TURN(), $order->getStatus());

        $order = new ReturnOrder('1', ReturnStatus::PARTRETURN(), 6000);
        $this->assertEquals(ReturnStatus::PARTRETURN(), $order->getStatus());
    }

    /**
     * @return void
     */
    public function testGetOrderId()
    {
        $order = new ReturnOrder('1', ReturnStatus::RE_TURN(), 6000);
        $this->assertEquals('1', $order->getOrderId());

        $order = new ReturnOrder('100', ReturnStatus::RE_TURN(), 6000);
        $this->assertEquals('100', $order->getOrderId());
    }

    /**
     * @return void
     */
    public function testGetAmount()
    {
        $order = new ReturnOrder('1', ReturnStatus::RE_TURN(), 6000);
        $this->assertEquals(6000, $order->getAmount());

        $order = new ReturnOrder('100', ReturnStatus::RE_TURN(), 10000);
        $this->assertEquals(10000, $order->getAmount());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $order = new ReturnOrder('1', ReturnStatus::RE_TURN(), 6000);
        $expected = [
            'orderId' => '1',
            'status' => ReturnStatus::RE_TURN()->getValue(),
            'amount' => 6000,
        ];

        $this->assertEquals($expected, $order->toArray());
    }
}

