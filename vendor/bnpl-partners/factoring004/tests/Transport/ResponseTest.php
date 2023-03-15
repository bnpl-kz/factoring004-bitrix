<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\DataSerializationException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     */
    public function testCreateFromPsrResponse(): void
    {
        $psrResponse = new PsrResponse();
        $response = new Response(200, [], []);
        $this->assertEquals($response, Response::createFromPsrResponse($psrResponse));

        $psrResponse = new PsrResponse(400);
        $response = new Response(400, [], []);
        $this->assertEquals($response, Response::createFromPsrResponse($psrResponse));

        $psrResponse = (new PsrResponse())->withHeader('Content-Type', 'application/json');
        $response = new Response(200, ['Content-Type' => 'application/json'], []);
        $this->assertEquals($response, Response::createFromPsrResponse($psrResponse));

        $psrResponse = (new PsrResponse())->withHeader('Content-Type', 'application/json')
            ->withBody(Utils::streamFor('{"a":15,"b":"test","c":[20]}'));
        $response = new Response(200, ['Content-Type' => 'application/json'], ['a' => 15, 'b' => 'test', 'c' => [20]]);
        $this->assertEquals($response, Response::createFromPsrResponse($psrResponse));

        $psrResponse = (new PsrResponse())->withHeader('Content-Type', 'application/json')
            ->withBody(Utils::streamFor('{"a:15}'));
        $this->expectException(DataSerializationException::class);
        Response::createFromPsrResponse($psrResponse);
    }

    public function testGetStatusCode(): void
    {
        $response = new Response(200, []);
        $this->assertEquals(200, $response->getStatusCode());

        $response = new Response(400, []);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testGetHeaders(): void
    {
        $response = new Response(200, []);
        $this->assertEmpty($response->getHeaders());

        $response = new Response(400, ['Content-Type' => 'application/json']);
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    public function testGetBody(): void
    {
        $response = new Response(200, []);
        $this->assertEmpty($response->getBody());

        $response = new Response(400, [], ['a' => 15, 'b' => 'test', 'c' => [20]]);
        $this->assertEquals(['a' => 15, 'b' => 'test', 'c' => [20]], $response->getBody());
    }
}

