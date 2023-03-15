<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class ReturnOrderTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new ReturnOrder('1', ReturnStatus::RETURN(), 6000);
        $actual = ReturnOrder::createFromArray([
            'orderId' => '1',
            'status' => ReturnStatus::RETURN()->getValue(),
            'amount' => 6000,
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetStatus(): void
    {
        $order = new ReturnOrder('1', ReturnStatus::RETURN(), 6000);
        $this->assertEquals(ReturnStatus::RETURN(), $order->getStatus());

        $order = new ReturnOrder('1', ReturnStatus::PARTRETURN(), 6000);
        $this->assertEquals(ReturnStatus::PARTRETURN(), $order->getStatus());
    }

    public function testGetOrderId(): void
    {
        $order = new ReturnOrder('1', ReturnStatus::RETURN(), 6000);
        $this->assertEquals('1', $order->getOrderId());

        $order = new ReturnOrder('100', ReturnStatus::RETURN(), 6000);
        $this->assertEquals('100', $order->getOrderId());
    }

    public function testGetAmount(): void
    {
        $order = new ReturnOrder('1', ReturnStatus::RETURN(), 6000);
        $this->assertEquals(6000, $order->getAmount());

        $order = new ReturnOrder('100', ReturnStatus::RETURN(), 10_000);
        $this->assertEquals(10_000, $order->getAmount());
    }

    public function testToArray(): void
    {
        $order = new ReturnOrder('1', ReturnStatus::RETURN(), 6000);
        $expected = [
            'orderId' => '1',
            'status' => ReturnStatus::RETURN()->getValue(),
            'amount' => 6000,
        ];

        $this->assertEquals($expected, $order->toArray());
    }
}

