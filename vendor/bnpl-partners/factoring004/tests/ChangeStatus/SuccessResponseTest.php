<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class SuccessResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new SuccessResponse('test', 'message');
        $actual = SuccessResponse::createFromArray(['error' => 'test', 'msg' => 'message']);
        $this->assertEquals($expected, $actual);

        $expected = new SuccessResponse('test', 'message', '100');
        $actual = SuccessResponse::createFromArray(['error' => 'test', 'msg' => 'message', 'merchantOrderId' => '100']);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetError()
    {
        $response = new SuccessResponse('test', 'message');
        $this->assertEquals('test', $response->getError());

        $response = new SuccessResponse('error', 'message');
        $this->assertEquals('error', $response->getError());
    }

    /**
     * @return void
     */
    public function testGetMsg()
    {
        $response = new SuccessResponse('test', 'message');
        $this->assertEquals('message', $response->getMsg());

        $response = new SuccessResponse('error', 'test');
        $this->assertEquals('test', $response->getMsg());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $response = new SuccessResponse('test', 'message');
        $this->assertEmpty($response->getMerchantOrderId());

        $response = new SuccessResponse('error', 'test', '100');
        $this->assertEquals('100', $response->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $response = new SuccessResponse('test', 'message');
        $expected = [
            'error' => 'test',
            'msg' => 'message',
            'merchantOrderId' => '',
        ];
        $this->assertEquals($expected, $response->toArray());

        $response = new SuccessResponse('test', 'message', '100');
        $expected = [
            'error' => 'test',
            'msg' => 'message',
            'merchantOrderId' => '100',
        ];
        $this->assertEquals($expected, $response->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $response = new SuccessResponse('test', 'message');
        $expected = [
            'error' => 'test',
            'msg' => 'message',
            'merchantOrderId' => '',
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));

        $response = new SuccessResponse('test', 'message', '100');
        $expected = [
            'error' => 'test',
            'msg' => 'message',
            'merchantOrderId' => '100',
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));
    }
}

