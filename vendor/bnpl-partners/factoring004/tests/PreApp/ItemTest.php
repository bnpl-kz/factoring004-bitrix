<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractTestCase;

class ItemTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new Item('1', 'test', 1, 6000, 8000);
        $actual = Item::createFromArray([
            'itemId' => '1',
            'itemName' => 'test',
            'itemQuantity' => 1,
            'itemPrice' => 6000,
            'itemSum' => 8000,
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new Item('1', 'test', 1, 6000, 8000);
        $expected->setItemCategory('1');
        $actual = Item::createFromArray([
            'itemId' => '1',
            'itemName' => 'test',
            'itemCategory' => '1',
            'itemQuantity' => 1,
            'itemPrice' => 6000,
            'itemSum' => 8000,
        ]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetItemSum()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(8000, $item->getItemSum());

        $item = new Item('1', 'test', 1, 6000, 10000);
        $this->assertEquals(10000, $item->getItemSum());
    }

    /**
     * @return void
     */
    public function testGetItemPrice()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(6000, $item->getItemPrice());

        $item = new Item('1', 'test', 1, 10000, 8000);
        $this->assertEquals(10000, $item->getItemPrice());
    }

    /**
     * @return void
     */
    public function testGetItemName()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals('test', $item->getItemName());

        $item = new Item('1', 'name', 1, 6000, 8000);
        $this->assertEquals('name', $item->getItemName());
    }

    /**
     * @return void
     */
    public function testGetItemCategory()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertNull($item->getItemCategory());

        $item = new Item('1', 'name', 1, 6000, 8000);
        $item->setItemCategory('100');
        $this->assertEquals('100', $item->getItemCategory());
    }

    /**
     * @return void
     */
    public function testGetItemQuantity()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(1, $item->getItemQuantity());

        $item = new Item('1', 'name', 10, 6000, 8000);
        $this->assertEquals(10, $item->getItemQuantity());
    }

    /**
     * @return void
     */
    public function testGetItemId()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals('1', $item->getItemId());

        $item = new Item('100', 'name', 10, 6000, 8000);
        $this->assertEquals('100', $item->getItemId());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $expected = [
            'itemId' => '1',
            'itemName' => 'test',
            'itemQuantity' => 1,
            'itemPrice' => 6000,
            'itemSum' => 8000,
        ];
        $this->assertEquals($expected, $item->toArray());

        $item = new Item('1', 'test', 1, 6000, 8000);
        $item->setItemCategory('1');
        $expected = [
            'itemId' => '1',
            'itemName' => 'test',
            'itemCategory' => '1',
            'itemQuantity' => 1,
            'itemPrice' => 6000,
            'itemSum' => 8000,
        ];
        $this->assertEquals($expected, $item->toArray());
    }
}

