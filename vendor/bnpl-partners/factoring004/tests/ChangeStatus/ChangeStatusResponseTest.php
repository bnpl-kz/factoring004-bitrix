<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class ChangeStatusResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new ChangeStatusResponse([], []);
        $actual = ChangeStatusResponse::createFromArray(['SuccessfulResponses' => [], 'ErrorResponses' => []]);
        $this->assertEquals($expected, $actual);

        $expected = new ChangeStatusResponse([], []);
        $actual = ChangeStatusResponse::createFromArray(['successfulResponses' => [], 'errorResponses' => []]);
        $this->assertEquals($expected, $actual);

        $expected = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            [new ErrorResponse('code', 'error', 'message')]
        );
        $actual = ChangeStatusResponse::createFromArray([
            'SuccessfulResponses' => [['error' => '', 'msg' => 'message']],
            'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message']],
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            [new ErrorResponse('code', 'error', 'message')]
        );
        $actual = ChangeStatusResponse::createFromArray([
            'successfulResponses' => [['error' => '', 'msg' => 'message']],
            'errorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message']],
        ]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetSuccessfulResponses()
    {
        $response = new ChangeStatusResponse([], []);
        $this->assertEmpty($response->getSuccessfulResponses());

        $response = new ChangeStatusResponse([new SuccessResponse('', 'test')], []);
        $this->assertEquals([new SuccessResponse('', 'test')], $response->getSuccessfulResponses());
    }

    /**
     * @return void
     */
    public function testGetErrorResponses()
    {
        $response = new ChangeStatusResponse([], []);
        $this->assertEmpty($response->getErrorResponses());

        $response = new ChangeStatusResponse([], [new ErrorResponse('code', 'error', 'message')]);
        $this->assertEquals([new ErrorResponse('code', 'error', 'message')], $response->getErrorResponses());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $response = new ChangeStatusResponse([], []);
        $expected = ['SuccessfulResponses' => [], 'ErrorResponses' => []];
        $this->assertEquals($expected, $response->toArray());

        $response = new ChangeStatusResponse([new SuccessResponse('', 'message')], [new ErrorResponse('code', 'error', 'message')]);
        $expected = [
            'SuccessfulResponses' => [['error' => '', 'msg' => 'message', 'merchantOrderId' => '']],
            'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message', 'merchantOrderId' => '']],
        ];
        $this->assertEquals($expected, $response->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $response = new ChangeStatusResponse([], []);
        $expected = ['SuccessfulResponses' => [], 'ErrorResponses' => []];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));

        $response = new ChangeStatusResponse([new SuccessResponse('', 'message')], [new ErrorResponse('code', 'error', 'message')]);
        $expected = [
            'SuccessfulResponses' => [['error' => '', 'msg' => 'message', 'merchantOrderId' => '']],
            'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message', 'merchantOrderId' => '']],
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));
    }
}

