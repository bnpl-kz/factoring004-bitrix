<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Response;

use PHPUnit\Framework\TestCase;

class ErrorResponseTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new ErrorResponse('100', 'test', 'test');
        $actual = ErrorResponse::createFromArray(['code' => '100', 'message' => 'test', 'description' => 'test']);
        $this->assertEquals($actual, $expected);

        $expected = new ErrorResponse('100', 'test', 'test', 'test', 'error');
        $actual = ErrorResponse::createFromArray([
            'code' => '100',
            'message' => 'test',
            'description' => 'test',
            'type' => 'test',
            'error' => 'error',
        ]);
        $this->assertEquals($actual, $expected);
    }

    public function testGetCode(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $this->assertEquals('100', $response->getCode());

        $response = new ErrorResponse('200', 'test', 'desc', 'test');
        $this->assertEquals('200', $response->getCode());
    }

    public function testGetMessage(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $this->assertEquals('test', $response->getMessage());

        $response = new ErrorResponse('200', 'message', 'desc', 'test');
        $this->assertEquals('message', $response->getMessage());
    }

    public function testGetType(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $this->assertNull($response->getType());

        $response = new ErrorResponse('100', 'test', 'test', 'test');
        $this->assertEquals('test', $response->getType());
    }

    public function testGetError(): void
    {
        $response = new ErrorResponse('100', 'test', 'test', 'test');
        $this->assertNull($response->getError());

        $response = new ErrorResponse('100', 'test', 'test', 'test', 'error');
        $this->assertEquals('error', $response->getError());
    }

    public function testGetDescription(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $this->assertEquals('test', $response->getDescription());

        $response = new ErrorResponse('100', 'test', 'desc', 'test');
        $this->assertEquals('desc', $response->getDescription());
    }

    public function testToArray(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $expected = [
            'code' => '100',
            'message' => 'test',
            'description' => 'test',
        ];
        $this->assertEquals($expected, $response->toArray());

        $response = new ErrorResponse('200', 'message', 'desc', 'type', 'error');
        $expected = [
            'code' => '200',
            'message' => 'message',
            'description' => 'desc',
            'type' => 'type',
            'error' => 'error',
        ];
        $this->assertEquals($expected, $response->toArray());
    }

    public function testJsonSerialize(): void
    {
        $response = new ErrorResponse('100', 'test', 'test');
        $expected = json_encode($response->toArray());
        $this->assertJsonStringEqualsJsonString($expected, json_encode($response->jsonSerialize()));

        $response = new ErrorResponse('200', 'message', 'desc', 'type', 'error');
        $expected = json_encode($response->toArray());
        $this->assertJsonStringEqualsJsonString($expected, json_encode($response->jsonSerialize()));
    }
}

