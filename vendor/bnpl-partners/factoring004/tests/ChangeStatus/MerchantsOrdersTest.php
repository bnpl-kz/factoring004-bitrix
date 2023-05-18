<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class MerchantsOrdersTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
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

        $expected = new MerchantsOrders('1', [new ReturnOrder('1000', ReturnStatus::RE_TURN(), 6000)]);
        $actual = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => ReturnStatus::RE_TURN()->getValue(), 'amount' => 6000]],
        ]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetMerchantId()
    {
        $merchantOrders = new MerchantsOrders('1', [new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000)]);
        $this->assertEquals('1', $merchantOrders->getMerchantId());

        $merchantOrders = new MerchantsOrders('100', [new ReturnOrder('1000', ReturnStatus::RE_TURN(), 6000)]);
        $this->assertEquals('100', $merchantOrders->getMerchantId());
    }

    /**
     * @return void
     */
    public function testGetOrders()
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
            new ReturnOrder('1000', ReturnStatus::RE_TURN(), 6000),
            new ReturnOrder('2000', ReturnStatus::PARTRETURN(), 10000),
        ];
        $merchantOrders = new MerchantsOrders('100', $orders);
        $this->assertEquals($orders, $merchantOrders->getOrders());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $orders = [new DeliveryOrder('1000', DeliveryStatus::DELIVERY(), 6000)];
        $merchantOrders = new MerchantsOrders('1', $orders);
        $expected = [
            'merchantId' => '1',
            'orders' => array_map(function (AbstractMerchantOrder $order) {
                return $order->toArray();
            }, $orders),
        ];

        $this->assertEquals($expected, $merchantOrders->toArray());

        $orders = [
            new ReturnOrder('1000', ReturnStatus::RE_TURN(), 6000),
            new ReturnOrder('2000', ReturnStatus::PARTRETURN(), 10000),
        ];
        $merchantOrders = new MerchantsOrders('100', $orders);
        $expected = [
            'merchantId' => '100',
            'orders' => array_map(function (AbstractMerchantOrder $order) {
                return $order->toArray();
            }, $orders),
        ];

        $this->assertEquals($expected, $merchantOrders->toArray());
    }
}

