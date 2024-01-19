<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class MerchantsOrdersTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new MerchantsOrders('1', [new CancelOrder('1000', CancelStatus::CANCEL())]);
        $actual = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => CancelStatus::CANCEL()->getValue()]],
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new MerchantsOrders('1', [new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000)]);
        $actual = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => DeliveryStatus::DELIVERY()->getValue(), 'amount' => 6000]],
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new MerchantsOrders('1', [new ReturnOrder('1000', ReturnStatus::RETURN(), 6000)]);
        $actual = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => ReturnStatus::RETURN()->getValue(), 'amount' => 6000]],
        ]);
        $this->assertEquals($expected, $actual);
    }

    public function testGetMerchantId(): void
    {
        $merchantOrders = new MerchantsOrders('1', [new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000)]);
        $this->assertEquals('1', $merchantOrders->getMerchantId());

        $merchantOrders = new MerchantsOrders('100', [new ReturnOrder('1000', ReturnStatus::RETURN(), 6000)]);
        $this->assertEquals('100', $merchantOrders->getMerchantId());
    }

    public function testGetOrders(): void
    {
        $merchantOrders = new MerchantsOrders('1', []);
        $this->assertEmpty($merchantOrders->getOrders());

        $orders = [
            new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000),
            new DeliveryOrder('2000', DeliveryStatus::DELIVERY(), 6000),
        ];
        $merchantOrders = new MerchantsOrders('100', $orders);
        $this->assertEquals($orders, $merchantOrders->getOrders());

        $orders = [
            new ReturnOrder('1000', ReturnStatus::RETURN(), 6000),
            new ReturnOrder('2000', ReturnStatus::PARTRETURN(), 10_000),
        ];
        $merchantOrders = new MerchantsOrders('100', $orders);
        $this->assertEquals($orders, $merchantOrders->getOrders());
    }

    public function testToArray(): void
    {
        $orders = [new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000)];
        $merchantOrders = new MerchantsOrders('1', $orders);
        $expected = [
            'merchantId' => '1',
            'orders' => array_map(fn(AbstractMerchantOrder $order) => $order->toArray(), $orders),
        ];

        $this->assertEquals($expected, $merchantOrders->toArray());

        $orders = [
            new ReturnOrder('1000', ReturnStatus::RETURN(), 6000),
            new ReturnOrder('2000', ReturnStatus::PARTRETURN(), 10_000),
        ];
        $merchantOrders = new MerchantsOrders('100', $orders);
        $expected = [
            'merchantId' => '100',
            'orders' => array_map(fn(AbstractMerchantOrder $order) => $order->toArray(), $orders),
        ];

        $this->assertEquals($expected, $merchantOrders->toArray());
    }
}

