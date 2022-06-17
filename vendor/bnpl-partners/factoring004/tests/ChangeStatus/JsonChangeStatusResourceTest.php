<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Psr\Http\Client\ClientInterface;

class JsonChangeStatusResourceTest extends AbstractResourceTest
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testChangeStatusJson(): void
    {
        $orders = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => DeliveryStatus::DELIVERY()->getValue(), 'amount' => 6000]],
        ]);

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('PUT', '/accountingservice/1.0/changeStatus/json', [$orders->toArray()], [])
            ->willReturn(new Response(200, [], [
                'SuccessfulResponses' => [['error' => '', 'msg' => 'message']],
                'ErrorResponses' => [],
            ]));

        $resource = new ChangeStatusResource($transport, static::BASE_URI);
        $response = $resource->changeStatusJson([$orders]);
        $expected = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            []
        );

        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testChangeStatusJsonWithError(): void
    {
        $orders = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => DeliveryStatus::DELIVERY()->getValue(), 'amount' => 6000]],
        ]);

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('PUT', '/accountingservice/1.0/changeStatus/json', [$orders->toArray()], [])
            ->willReturn(new Response(200, [], [
                'SuccessfulResponses' => [],
                'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message']],
            ]));

        $resource = new ChangeStatusResource($transport, static::BASE_URI);
        $response = $resource->changeStatusJson([$orders]);
        $expected = new ChangeStatusResponse(
            [],
            [new ErrorResponse('code', 'error', 'message')]
        );

        $this->assertEquals($expected, $response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testChangeStatusJsonWithMixedResponse(): void
    {
        $orders = MerchantsOrders::createFromArray([
            'merchantId' => '1',
            'orders' => [['orderId' => '1000', 'status' => DeliveryStatus::DELIVERY()->getValue(), 'amount' => 6000]],
        ]);

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('PUT', '/accountingservice/1.0/changeStatus/json', [$orders->toArray()], [])
            ->willReturn(new Response(200, [], [
                'SuccessfulResponses' => [['error' => '', 'msg' => 'message']],
                'ErrorResponses' => [['code' => 'code', 'error' => 'error', 'message' => 'message']],
            ]));

        $resource = new ChangeStatusResource($transport, static::BASE_URI);
        $response = $resource->changeStatusJson([$orders]);
        $expected = new ChangeStatusResponse(
            [new SuccessResponse('', 'message')],
            [new ErrorResponse('code', 'error', 'message')]
        );

        $this->assertEquals($expected, $response);
    }

    protected function callResourceMethod(ClientInterface $client): void
    {
        $resource = new ChangeStatusResource($this->createTransport($client), static::BASE_URI);
        $resource->changeStatusJson([
            MerchantsOrders::createFromArray([
                'merchantId' => '1',
                'orders' => [['orderId' => '1000', 'status' => DeliveryStatus::DELIVERY()->getValue(), 'amount' => 6000]],
            ]),
        ]);
    }
}
