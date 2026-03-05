<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class DeliveryOrderTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $actual = DeliveryOrder::createFromArray([
            'orderId' => '1',
            'status' => DeliveryStatus::DELIVERY()->getValue(),
            'amount' => 6000
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetStatus(): void
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals(DeliveryStatus::DELIVERY(), $order->getStatus());
    }

    public function testGetOrderId(): void
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals('1', $order->getOrderId());

        $order = new DeliveryOrder('100', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals('100', $order->getOrderId());
    }

    public function getTestAmount(): void
    {
        $order = new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000);
        $this->assertEquals(6000, $order->getAmount());
    }

    public function testToArray(): void
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

