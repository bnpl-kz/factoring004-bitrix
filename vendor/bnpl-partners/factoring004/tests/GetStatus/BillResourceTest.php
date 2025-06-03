<?php

namespace BnplPartners\Factoring004\GetStatus;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class BillResourceTest extends \BnplPartners\Factoring004\AbstractResourceTest
{

    public function testGetStatus()
    {
        $client = $this->createStub(ClientInterface::class);
        $client->method('send')
            ->willReturn(new Response(200, [], json_encode(['status' => 'received'])));

        $resource = new BillResource($this->createTransport($client), self::BASE_URI);
        $response = $resource->getStatus('777');
        $expected = StatusResponse::create(['status' => 'received']);

        $this->assertEquals($expected, $response);
    }

    /**
     * @inheritDoc
     */
    protected function callResourceMethod(ClientInterface $client)
    {
        $resource = new BillResource($this->createTransport($client), static::BASE_URI);
        $resource->getStatus("777");
    }
}