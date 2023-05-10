<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\ChangeStatus\ErrorResponse as ChangeStatusErrorResponse;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;
use BnplPartners\Factoring004\Exception\ApiException;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\PackageException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;
use PHPUnit\Framework\TestCase;

class ChangeStatusCallerTest extends TestCase
{
    public function testCreate(): void
    {
        $expected = new ChangeStatusCaller(Api::create('http://example.com')->changeStatus, '1');
        $actual = ChangeStatusCaller::create(Api::create('http://example.com')->changeStatus, '1');
        $this->assertEquals($expected, $actual);

        $expected = new ChangeStatusCaller(Api::create('http://example.org')->changeStatus, '100');
        $actual = ChangeStatusCaller::create(Api::create('http://example.org')->changeStatus, '100');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider ordersProvider
     */
    public function testCall(string $merchantId, AbstractMerchantOrder $order): void
    {
        $orders = [new MerchantsOrders($merchantId, [$order])];
        $successResponse = new SuccessResponse('', 'Success', $order->getOrderId());

        $resource = $this->createMock(ChangeStatusResource::class);
        $resource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([$successResponse], []));

        $caller = new ChangeStatusCaller($resource, $merchantId);

        $this->assertEquals($successResponse, $caller->call($order));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionOrdersProvider
     */
    public function testCallWithException(
        string $merchantId,
        AbstractMerchantOrder $order,
        PackageException $exception
    ): void {
        $orders = [new MerchantsOrders($merchantId, [$order])];

        $resource = $this->createMock(ChangeStatusResource::class);
        $resource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willThrowException($exception);

        $caller = new ChangeStatusCaller($resource, $merchantId);
        $this->expectException(get_class($exception));

        $caller->call($order);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider errorOrdersProvider
     */
    public function testCallWithErrorResponse(
        string $merchantId,
        AbstractMerchantOrder $order,
        ChangeStatusErrorResponse $errorResponse
    ): void {
        $orders = [new MerchantsOrders($merchantId, [$order])];

        $resource = $this->createMock(ChangeStatusResource::class);
        $resource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([], [$errorResponse]));

        $caller = new ChangeStatusCaller($resource, $merchantId);
        $expectedErrorResponse = new ErrorResponse(
            $errorResponse->getCode(),
            $errorResponse->getMessage(),
            null,
            null,
            $errorResponse->getError(),
        );

        try {
            $caller->call($order);
        } catch (ErrorResponseException $e) {
            $this->assertEquals($expectedErrorResponse, $e->getErrorResponse());
        }
    }

    public function ordersProvider(): array
    {
        return [
            ['1', new DeliveryOrder('1', DeliveryStatus::DELIVERED(), 6000)],
            ['2', new DeliveryOrder('100', DeliveryStatus::DELIVERED(), 10_000)],

            ['10', new ReturnOrder('1', ReturnStatus::RETURN(), 6000)],
            ['10', new ReturnOrder('1', ReturnStatus::RETURN(), 0)],
            ['20', new ReturnOrder('100', ReturnStatus::PARTRETURN(), 6000)],

            ['1000', new CancelOrder('1', CancelStatus::CANCEL())],
            ['2000', new CancelOrder('100', CancelStatus::CANCEL())],
        ];
    }

    public function exceptionOrdersProvider(): array
    {
        $exceptions = [
            new NetworkException(),
            new DataSerializationException(),
            new TransportException(),
            new UnexpectedResponseException($this->createStub(ResponseInterface::class)),
            new EndpointUnavailableException($this->createStub(ResponseInterface::class)),
            new ErrorResponseException(new ErrorResponse('1', 'test')),
            new AuthenticationException('Invalid Credentials'),
            new ApiException(),
            new PackageException(),
        ];

        $result = [];

        foreach ($this->ordersProvider() as $item) {
            foreach ($exceptions as $exception) {
                $result[] = [...$item, $exception];
            }
        }

        return $result;
    }

    public function errorOrdersProvider(): array
    {
        $errors = [
            new ChangeStatusErrorResponse('1', 'error', 'test'),
            new ChangeStatusErrorResponse('100', 'Order Not Found', 'An error occurred'),
            new ChangeStatusErrorResponse('1000', 'Order is expired', 'Expired'),
        ];

        $result = [];

        foreach ($this->ordersProvider() as $item) {
            foreach ($errors as $error) {
                $result[] = [...$item, $error];
            }
        }

        return $result;
    }
}

