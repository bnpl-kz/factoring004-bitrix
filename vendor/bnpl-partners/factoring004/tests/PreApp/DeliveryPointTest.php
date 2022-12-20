<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractTestCase;

class DeliveryPointTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new DeliveryPoint();
        $actual = DeliveryPoint::createFromArray([]);
        $this->assertEquals($expected, $actual);

        $expected = (new DeliveryPoint())->setFlat('10')
            ->setCity('Almaty');

        $actual = DeliveryPoint::createFromArray([
            'flat' => '10',
            'city' => 'Almaty',
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testFlat()
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

    /**
     * @return void
     */
    public function testHouse()
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

    /**
     * @return void
     */
    public function testDistrict()
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

    /**
     * @return void
     */
    public function testRegion()
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

    /**
     * @return void
     */
    public function testCity()
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

    /**
     * @return void
     */
    public function testStreet()
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

    /**
     * @return void
     */
    public function testToArray()
    {
        $attributes = ['street' => '', 'house' => '', 'region' => '', 'city' => '', 'district' => '', 'flat' => ''];
        $deliveryPoint = new DeliveryPoint();
        $this->assertEquals($attributes, $deliveryPoint->toArray());

        $attributes = ['street' => 'Green', 'house' => '10', 'region' => 'Almaty', 'city' => '', 'district' => '', 'flat' => ''];
        $deliveryPoint = DeliveryPoint::createFromArray($attributes);
        $this->assertEquals($attributes, $deliveryPoint->toArray());
    }
}
