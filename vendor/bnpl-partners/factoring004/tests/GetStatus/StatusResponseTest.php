<?php

namespace BnplPartners\Factoring004\GetStatus;

class StatusResponseTest extends \PHPUnit\Framework\TestCase
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