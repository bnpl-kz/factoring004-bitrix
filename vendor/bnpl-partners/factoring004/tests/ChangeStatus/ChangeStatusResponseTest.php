<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class ChangeStatusResponseTest extends TestCase
{
    public function testCreateFromArray(): void
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

    public function testGetSuccessfulResponses(): void
    {
        $response = new ChangeStatusResponse([], []);
        $this->assertEmpty($response->getSuccessfulResponses());

        $response = new ChangeStatusResponse([new SuccessResponse('', 'test')], []);
        $this->assertEquals([new SuccessResponse('', 'test')], $response->getSuccessfulResponses());
    }

    public function testGetErrorResponses(): void
    {
        $response = new ChangeStatusResponse([], []);
        $this->assertEmpty($response->getErrorResponses());

        $response = new ChangeStatusResponse([], [new ErrorResponse('code', 'error', 'message')]);
        $this->assertEquals([new ErrorResponse('code', 'error', 'message')], $response->getErrorResponses());
    }

    public function testToArray(): void
    {
        $response = new ChangeStatusResponse([], []);
        $expected = ['SuccessfulResponses' => [], 'ErrorResponses' => []];
        $this->assertEquals($expected, $response->toArray());

        $response = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            [new ErrorResponse('code', 'error', 'message')],
        );
        $expected = [
            'SuccessfulResponses' => [['error' => '', 'msg' => 'message', 'merchantOrderId' => '']],
            'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message', 'merchantOrderId' => '']],
        ];
        $this->assertEquals($expected, $response->toArray());
    }

    public function testJsonSerialize(): void
    {
        $response = new ChangeStatusResponse([], []);
        $expected = ['SuccessfulResponses' => [], 'ErrorResponses' => []];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));

        $response = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            [new ErrorResponse('code', 'error', 'message')],
        );
        $expected = [
            'SuccessfulResponses' => [['error' => '', 'msg' => 'message', 'merchantOrderId' => '']],
            'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message', 'merchantOrderId' => '']],
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response));
    }
}

