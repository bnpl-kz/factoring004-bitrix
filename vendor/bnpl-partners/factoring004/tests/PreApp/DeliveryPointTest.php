<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use PHPUnit\Framework\TestCase;

class DeliveryPointTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new DeliveryPoint();
        $actual = DeliveryPoint::createFromArray([]);
        $this->assertEquals($expected, $actual);

        $expected = (new DeliveryPoint())
            ->setFlat('10')
            ->setCity('Almaty');

        $actual = DeliveryPoint::createFromArray([
            'flat' => '10',
            'city' => 'Almaty',
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testFlat(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getFlat());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setFlat('10');
        $this->assertEquals('10', $deliveryPoint->getFlat());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setFlat('10a');
        $this->assertEquals('10a', $deliveryPoint->getFlat());
    }

    public function testHouse(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getHouse());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setHouse('10');
        $this->assertEquals('10', $deliveryPoint->getHouse());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setHouse('10/15');
        $this->assertEquals('10/15', $deliveryPoint->getHouse());
    }

    public function testDistrict(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getDistrict());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setDistrict('test');
        $this->assertEquals('test', $deliveryPoint->getDistrict());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setDistrict('10 district');
        $this->assertEquals('10 district', $deliveryPoint->getDistrict());
    }

    public function testRegion(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getRegion());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setRegion('Almaty');
        $this->assertEquals('Almaty', $deliveryPoint->getRegion());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setRegion('Almaty region');
        $this->assertEquals('Almaty region', $deliveryPoint->getRegion());
    }

    public function testCity(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getCity());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setCity('Almaty');
        $this->assertEquals('Almaty', $deliveryPoint->getCity());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setCity('Karaganda');
        $this->assertEquals('Karaganda', $deliveryPoint->getCity());
    }

    public function testStreet(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->assertEmpty($deliveryPoint->getStreet());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setStreet('Green');
        $this->assertEquals('Green', $deliveryPoint->getStreet());

        $deliveryPoint = new DeliveryPoint();
        $deliveryPoint->setStreet('Green street');
        $this->assertEquals('Green street', $deliveryPoint->getStreet());
    }

    public function testToArray(): void
    {
        $attributes = ['street' => '', 'house' => '', 'region' => '', 'city' => '', 'district' => '', 'flat' => ''];
        $deliveryPoint = new DeliveryPoint();
        $this->assertEquals($attributes, $deliveryPoint->toArray());

        $attributes = ['street' => 'Green', 'house' => '10', 'region' => 'Almaty', 'city' => '', 'district' => '', 'flat' => ''];
        $deliveryPoint = DeliveryPoint::createFromArray($attributes);
        $this->assertEquals($attributes, $deliveryPoint->toArray());
    }
}
