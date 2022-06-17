<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class CancelOrderTest extends TestCase
{
    public function testCreateFromArray()
    {
        $expected = new CancelOrder('1', CancelStatus::CANCEL());
        $actual = CancelOrder::createFromArray([
            'orderId' => '1',
            'status' => CancelStatus::CANCEL()->getValue(),
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetStatus()
    {
        $order = new CancelOrder('1', CancelStatus::CANCEL());
        $this->assertEquals(CancelStatus::CANCEL(), $order->getStatus());
    }
}
