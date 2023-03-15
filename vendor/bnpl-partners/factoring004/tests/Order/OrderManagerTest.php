<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionProperty;

class OrderManagerTest extends TestCase
{
    public function testCreate(): void
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
     */
    public function testDelivery(string $merchantId, string $orderId, int $amount): void
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->delivery($merchantId, $orderId, $amount);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals($amount, $this->getPropertyValue($confirmation, 'amount'));
    }

    /**
     * @dataProvider ordersProvider
     */
    public function testFullRefund(string $merchantId, string $orderId): void
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->fullRefund($merchantId, $orderId);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals(0, $this->getPropertyValue($confirmation, 'amount'));
    }

    /**
     * @dataProvider ordersProvider
     */
    public function testPartialRefund(string $merchantId, string $orderId, int $amount): void
    {
        $manager = OrderManager::create('http://example.com');
        $confirmation = $manager->partialRefund($merchantId, $orderId, $amount);

        $this->assertEquals($merchantId, $this->getPropertyValue($confirmation, 'merchantId'));
        $this->assertEquals($orderId, $this->getPropertyValue($confirmation, 'orderId'));
        $this->assertEquals($amount, $this->getPropertyValue($confirmation, 'amount'));
    }

    public function ordersProvider(): array
    {
        return [
            ['1', '1', 6000],
            ['2', '10', 8000],
            ['3', '100', 10_000],
        ];
    }

    /**
     * @return mixed
     */
    private function getPropertyValue(StatusConfirmationInterface $confirmation, string $propertyName)
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

