<?php

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\DataSerializationException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use GuzzleHttp\Psr7\Utils;
use BnplPartners\Factoring004\AbstractTestCase;

class ResponseTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @return void
     */
    public function testCreateFromPsrResponse()
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

    /**
     * @return void
     */
    public function testGetStatusCode()
    {
        $response = new Response(200, []);
        $this->assertEquals(200, $response->getStatusCode());

        $response = new Response(400, []);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @return void
     */
    public function testGetHeaders()
    {
        $response = new Response(200, []);
        $this->assertEmpty($response->getHeaders());

        $response = new Response(400, ['Content-Type' => 'application/json']);
        $this->assertEquals(['Content-Type' => 'application/json'], $response->getHeaders());
    }

    /**
     * @return void
     */
    public function testGetBody()
    {
        $response = new Response(200, []);
        $this->assertEmpty($response->getBody());

        $response = new Response(400, [], ['a' => 15, 'b' => 'test', 'c' => [20]]);
        $this->assertEquals(['a' => 15, 'b' => 'test', 'c' => [20]], $response->getBody());
    }
}

