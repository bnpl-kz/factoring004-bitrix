<?php

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\PreApp\Status;
use BnplPartners\Factoring004\AbstractTestCase;

class PreAppResponseTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $actual = PreAppResponse::createFromArray([
            'status' => Status::RECEIVED()->getValue(),
            'preappId' => 'id-1',
            'redirectLink' => 'http://example.com',
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetStatus()
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals(Status::RECEIVED(), $response->getStatus());

        $response = new PreAppResponse(Status::ERROR(), 'id-1', 'http://example.com');
        $this->assertEquals(Status::ERROR(), $response->getStatus());
    }

    /**
     * @return void
     */
    public function testGetRedirectLink()
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals('http://example.com', $response->getRedirectLink());

        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.org');
        $this->assertEquals('http://example.org', $response->getRedirectLink());
    }

    /**
     * @return void
     */
    public function testGetPreAppId()
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals('id-1', $response->getPreAppId());

        $response = new PreAppResponse(Status::RECEIVED(), 'id-2', 'http://example.com');
        $this->assertEquals('id-2', $response->getPreAppId());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $expected = [
            'status' => Status::RECEIVED()->getValue(),
            'preappId' => 'id-1',
            'redirectLink' => 'http://example.com',
        ];

        $this->assertEquals($expected, $response->toArray());

        $response = new PreAppResponse(Status::ERROR(), 'id-2', 'http://example.org');
        $expected = [
            'status' => Status::ERROR()->getValue(),
            'preappId' => 'id-2',
            'redirectLink' => 'http://example.org',
        ];

        $this->assertEquals($expected, $response->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $expected = [
            'status' => Status::RECEIVED()->getValue(),
            'preappId' => 'id-1',
            'redirectLink' => 'http://example.com',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response->jsonSerialize()));

        $response = new PreAppResponse(Status::ERROR(), 'id-2', 'http://example.org');
        $expected = [
            'status' => Status::ERROR()->getValue(),
            'preappId' => 'id-2',
            'redirectLink' => 'http://example.org',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($response->jsonSerialize()));
    }
}

