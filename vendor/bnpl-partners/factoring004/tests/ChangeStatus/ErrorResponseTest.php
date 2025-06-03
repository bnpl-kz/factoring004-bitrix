<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class ErrorResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
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

    /**
     * @return void
     */
    public function testGetError()
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('error', $response->getError());

        $response = new ErrorResponse('code', 'test', 'message');
        $this->assertEquals('test', $response->getError());
    }

    /**
     * @return void
     */
    public function testGetMessage()
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('message', $response->getMessage());

        $response = new ErrorResponse('code', 'error', 'test');
        $this->assertEquals('test', $response->getMessage());
    }

    /**
     * @return void
     */
    public function testGetCode()
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEquals('code', $response->getCode());

        $response = new ErrorResponse('test', 'error', 'message');
        $this->assertEquals('test', $response->getCode());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $response = new ErrorResponse('code', 'error', 'message');
        $this->assertEmpty($response->getMerchantOrderId());

        $response = new ErrorResponse('test', 'error', 'message', '100');
        $this->assertEquals('100', $response->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testToArray()
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

    /**
     * @return void
     */
    public function testJsonSerialize()
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

