<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\GetStatus;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\ClientInterface;

class BillResourceTest extends AbstractResourceTest
{
    public function testGetStatus()
    {
        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('GET', '/bnpl/bill/777/status', [], [])
            ->willReturn(new Response(200, [], ['status' => 'received']));

        $resource = new BillResource($transport, self::BASE_URI);
        $response = $resource->getStatus('777');
        $expected = StatusResponse::create(['status' => 'received']);

        $this->assertEquals($expected, $response);
    }

    protected function callResourceMethod(ClientInterface $client): void
    {
        $resource = new BillResource($this->createTransport($client), self::BASE_URI);
        $resource->getStatus('777');
    }
}