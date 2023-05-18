<?php

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\PreApp\ValidationErrorDetail;
use BnplPartners\Factoring004\AbstractTestCase;

class ValidationErrorResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $detail = new ValidationErrorDetail('test', 'expiresAt');
        $expected = new ValidationErrorResponse(1, [$detail], 'test');
        $actual = ValidationErrorResponse::createFromArray([
            'code' => 1,
            'details' => [$detail->toArray()],
            'message' => 'test',
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetCode()
    {
        $error = new ValidationErrorResponse(1, [new ValidationErrorDetail('test', 'expiresAt')], 'test');
        $this->assertEquals(1, $error->getCode());

        $error = new ValidationErrorResponse(3, [new ValidationErrorDetail('test', 'expiresAt')], 'test');
        $this->assertEquals(3, $error->getCode());
    }

    /**
     * @return void
     */
    public function testGetMessage()
    {
        $error = new ValidationErrorResponse(1, [new ValidationErrorDetail('test', 'expiresAt')], 'test');
        $this->assertEquals('test', $error->getMessage());

        $error = new ValidationErrorResponse(3, [new ValidationErrorDetail('test', 'expiresAt')], 'message');
        $this->assertEquals('message', $error->getMessage());
    }

    /**
     * @return void
     */
    public function testGetDetails()
    {
        $details = [new ValidationErrorDetail('test', 'expiresAt')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $this->assertEquals($details, $error->getDetails());

        $details = [new ValidationErrorDetail('test', 'expiresAt'), new ValidationErrorDetail('error', 'deliveryDate')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $this->assertEquals($details, $error->getDetails());

        $details = [];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $this->assertEmpty($error->getDetails());
    }

    /**
     * @return void
     */
    public function testGetPrefix()
    {
        $error = new ValidationErrorResponse(1, [new ValidationErrorDetail('test', 'expiresAt')], 'test');
        $this->assertNull($error->getPrefix());

        $error = new ValidationErrorResponse(1, [new ValidationErrorDetail('test', 'expiresAt')], 'test', 'test');
        $this->assertEquals('test', $error->getPrefix());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $details = [new ValidationErrorDetail('test', 'expiresAt')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $expected = [
            'code' => 1,
            'details' => [$details[0]->toArray()],
            'message' => 'test',
        ];

        $this->assertEquals($expected, $error->toArray());

        $details = [new ValidationErrorDetail('test', 'expiresAt'), new ValidationErrorDetail('error', 'deliveryDate')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $expected = [
            'code' => 1,
            'details' => [$details[0]->toArray(), $details[1]->toArray()],
            'message' => 'test',
        ];

        $this->assertEquals($expected, $error->toArray());

        $error = new ValidationErrorResponse(1, [], 'test', 'test');
        $expected = [
            'code' => 1,
            'details' => [],
            'message' => 'test',
            'prefix' => 'test',
        ];

        $this->assertEquals($expected, $error->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $details = [new ValidationErrorDetail('test', 'expiresAt')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $expected = [
            'code' => 1,
            'details' => [$details[0]->toArray()],
            'message' => 'test',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($error->jsonSerialize()));

        $details = [new ValidationErrorDetail('test', 'expiresAt'), new ValidationErrorDetail('error', 'deliveryDate')];
        $error = new ValidationErrorResponse(1, $details, 'test');
        $expected = [
            'code' => 1,
            'details' => [$details[0]->toArray(), $details[1]->toArray()],
            'message' => 'test',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($error->jsonSerialize()));

        $error = new ValidationErrorResponse(1, [], 'test', 'test');
        $expected = [
            'code' => 1,
            'details' => [],
            'message' => 'test',
            'prefix' => 'test',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($error->jsonSerialize()));
    }
}

