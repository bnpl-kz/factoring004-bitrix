<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreateFromArray(): void
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

    public function testGetItemSum(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(8000, $item->getItemSum());

        $item = new Item('1', 'test', 1, 6000, 10_000);
        $this->assertEquals(10_000, $item->getItemSum());
    }

    public function testGetItemPrice(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(6000, $item->getItemPrice());

        $item = new Item('1', 'test', 1, 10_000, 8000);
        $this->assertEquals(10_000, $item->getItemPrice());
    }

    public function testGetItemName(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals('test', $item->getItemName());

        $item = new Item('1', 'name', 1, 6000, 8000);
        $this->assertEquals('name', $item->getItemName());
    }

    public function testGetItemCategory(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertNull($item->getItemCategory());

        $item = new Item('1', 'name', 1, 6000, 8000);
        $item->setItemCategory('100');
        $this->assertEquals('100', $item->getItemCategory());
    }

    public function testGetItemQuantity(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals(1, $item->getItemQuantity());

        $item = new Item('1', 'name', 10, 6000, 8000);
        $this->assertEquals(10, $item->getItemQuantity());
    }

    public function testGetItemId(): void
    {
        $item = new Item('1', 'test', 1, 6000, 8000);
        $this->assertEquals('1', $item->getItemId());

        $item = new Item('100', 'name', 10, 6000, 8000);
        $this->assertEquals('100', $item->getItemId());
    }

    public function testToArray(): void
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

