<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\PreApp\Status;
use PHPUnit\Framework\TestCase;

class PreAppResponseTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $actual = PreAppResponse::createFromArray([
            'status' => Status::RECEIVED()->getValue(),
            'preappId' => 'id-1',
            'redirectLink' => 'http://example.com',
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetStatus(): void
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals(Status::RECEIVED(), $response->getStatus());

        $response = new PreAppResponse(Status::ERROR(), 'id-1', 'http://example.com');
        $this->assertEquals(Status::ERROR(), $response->getStatus());
    }

    public function testGetRedirectLink(): void
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals('http://example.com', $response->getRedirectLink());

        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.org');
        $this->assertEquals('http://example.org', $response->getRedirectLink());
    }

    public function testGetPreAppId(): void
    {
        $response = new PreAppResponse(Status::RECEIVED(), 'id-1', 'http://example.com');
        $this->assertEquals('id-1', $response->getPreAppId());

        $response = new PreAppResponse(Status::RECEIVED(), 'id-2', 'http://example.com');
        $this->assertEquals('id-2', $response->getPreAppId());
    }

    public function testToArray(): void
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

    public function testJsonSerialize(): void
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

