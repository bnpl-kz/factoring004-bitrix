<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use InvalidArgumentException;
use BnplPartners\Factoring004\AbstractTestCase;
use ReflectionException;
use ReflectionProperty;

class OrderManagerTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $expected = new OrderManager(Api::create('http://example.com'));
        $actual = OrderManager::create('http://example.com');
        $this->assertEquals($expected, $actual);

        $expected = new OrderManager(Api::create('http://example.com', new BearerTokenAuth('Test')));
        $actual = OrderManager::create('http://example.com', new BearerTokenAuth('Test'));
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider ordersProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     */
    public function testDelivery($merchantId, $orderId, $amount)
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->delivery($merchantId, $orderId, $amount);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals($amount, $this->getPropertyValue($confirmation, 'amount'));
    }

    /**
     * @dataProvider ordersProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     */
    public function testFullRefund($merchantId, $orderId)
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->fullRefund($merchantId, $orderId);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals(0, $this->getPropertyValue($confirmation, 'amount'));
    }

    /**
     * @dataProvider ordersProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     */
    public function testPartialRefund($merchantId, $orderId, $amount)
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->partialRefund($merchantId, $orderId, $amount);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals($amount, $this->getPropertyValue($confirmation, 'amount'));
    }

    /**
     * @return mixed[]
     */
    public function ordersProvider()
    {
        return [
            ['1', '1', 6000],
            ['2', '10', 8000],
            ['3', '100', 10000],
        ];
    }

    /**
     * @return mixed
     * @param string $propertyName
     */
    private function getPropertyValue(StatusConfirmationInterface $confirmation, $propertyName)
    {
        try {
            $refProperty = new ReflectionProperty($confirmation, $propertyName);
            $refProperty->setAccessible(true);
            return $refProperty->getValue($confirmation);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("Property {$propertyName} does not exist", 0, $e);
        }
    }
}

