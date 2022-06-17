<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new ErrorResponse('code', 'error', 'message');
        $actual = ErrorResponse::createFromArray([
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new ErrorResponse('code', 'error', 'message', '100');
        $actual = ErrorResponse::createFromArray([
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
            'merchantOrderId' => '100',
        ]);
        $this->assertEquals($expected, $actual);
    }

    public function testGetError(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('error', $response->getError());

        $response = new ErrorResponse('code', 'test', 'message');
        $this->assertEquals('test', $response->getError());
    }

    public function testGetMessage(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('message', $response->getMessage());

        $response = new ErrorResponse('code', 'error', 'test');
        $this->assertEquals('test', $response->getMessage());
    }

    public function testGetCode(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('code', $response->getCode());

        $response = new ErrorResponse('test', 'error', 'message');
        $this->assertEquals('test', $response->getCode());
    }

    public function testGetMerchantOrderId(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEmpty($response->getMerchantOrderId());

        $response = new ErrorResponse('test', 'error', 'message', '100');
        $this->assertEquals('100', $response->getMerchantOrderId());
    }

    public function testToArray(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $expected = [
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
            'merchantOrderId' => '',
        ];
        $this->assertEquals($expected, $response->toArray());

        $response = new ErrorResponse('code', 'error', 'message', '100');
        $expected = [
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
            'merchantOrderId' => '100',
        ];
        $this->assertEquals($expected, $response->toArray());
    }

    public function testJsonSerialize(): void
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $expected = [
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
            'merchantOrderId' => '',
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));

        $response = new ErrorResponse('code', 'error', 'message', '100');
        $expected = [
            'code' => 'code',
            'error' => 'error',
            'message' => 'message',
            'merchantOrderId' => '100',
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));
    }
}

