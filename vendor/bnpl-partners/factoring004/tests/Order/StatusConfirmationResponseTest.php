<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use PHPUnit\Framework\TestCase;

class StatusConfirmationResponseTest extends TestCase
{
    public function testCreate(): void
    {
        $expected = new StatusConfirmationResponse('Test');
        $actual = StatusConfirmationResponse::create('Test');
        $this->assertEquals($expected, $actual);

        $expected = new StatusConfirmationResponse('Message');
        $actual = StatusConfirmationResponse::create('Message');
        $this->assertEquals($expected, $actual);
    }

    public function testGetMessage(): void
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertEquals('Test', $response->getMessage());

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals('Message', $response->getMessage());
    }

    public function testToArray(): void
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertEquals(['message' => 'Test'], $response->toArray());

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals(['message' => 'Message'], $response->toArray());
    }

    public function testJsonSerialize(): void
    {
        $response = new StatusConfirmationResponse('Test');
        $this->assertJsonStringEqualsJsonString(json_encode(['message' => 'Test']), json_encode($response));

        $response = new StatusConfirmationResponse('Message');
        $this->assertEquals(json_encode(['message' => 'Message']), json_encode($response));
    }
}

