<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\AbstractTestCase;

class StatusConfirmationResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $expected = new StatusConfirmationResponse('Test');
        $actual = StatusConfirmationResponse::create('Test');
        $this->assertEquals($expected, $actual);

        $expected = new StatusConfirmationResponse('Message');
        $actual = StatusConfirmationResponse::create('Message');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetMessage()
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertEquals('Test', $response->getMessage());

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals('Message', $response->getMessage());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertEquals(['message' => 'Test'], $response->toArray());

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals(['message' => 'Message'], $response->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertJsonStringEqualsJsonString(json_encode(['message' => 'Test']), json_encode($response));

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals(json_encode(['message' => 'Message']), json_encode($response));
    }
}

