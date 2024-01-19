<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\GetStatus;

use PHPUnit\Framework\TestCase;

class StatusResponseTest extends TestCase
{
    public function testCreate()
    {
        $expected = new StatusResponse('received');
        $actual = StatusResponse::create(['status' => 'received']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetStatus()
    {
        $expected = 'received';
        $actual = StatusResponse::create(['status' => 'received']);
        $this->assertEquals($expected, $actual->getStatus());
    }
}