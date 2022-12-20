<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse;
use BnplPartners\Factoring004\ChangeStatus\ErrorResponse as ChangeStatusErrorResponse;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
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
use BnplPartners\Factoring004\AbstractTestCase;

class OrderManagerCancelTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider ordersProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param string $message
     */
    public function testCancel($merchantId, $orderId, $message)
    {
        $orders = [
            new MerchantsOrders($merchantId, [new CancelOrder($orderId, CancelStatus::CANCEL())]),
        ];

        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([new SuccessResponse('', $message)], []));

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('changeStatus')
            ->willReturn($changeStatusResource);

        $manager = new OrderManager($api);

        $this->assertEquals(new StatusConfirmationResponse($message), $manager->cancel($merchantId, $orderId));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @testWith ["1", "1", "1", "error", "message"]
     *           ["2", "10", "100", "test", "an error occurred"]
     *           ["3", "100", "200", "failed", "order cancellation error"]
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param string $code
     * @param string $error
     * @param string $message
     */
    public function testCancelWithError(
        $merchantId,
        $orderId,
        $code,
        $error,
        $message
    ) {
        $orders = [
            new MerchantsOrders($merchantId, [new CancelOrder($orderId, CancelStatus::CANCEL())]),
        ];

        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([], [new ChangeStatusErrorResponse($code, $error, $message)]));

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('changeStatus')
            ->willReturn($changeStatusResource);

        $manager = new OrderManager($api);

        try {
            $manager->cancel($merchantId, $orderId);
        } catch (ErrorResponseException $e) {
            $this->assertEquals($code, $e->getErrorResponse()->getCode());
            $this->assertEquals($error, $e->getErrorResponse()->getError());
            $this->assertEquals($message, $e->getErrorResponse()->getMessage());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     */
    public function testCancelThrowsException($merchantId, $orderId, PackageException $exception)
    {
        $orders = [
            new MerchantsOrders($merchantId, [new CancelOrder($orderId, CancelStatus::CANCEL())]),
        ];

        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willThrowException($exception);

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('changeStatus')
            ->willReturn($changeStatusResource);

        $manager = new OrderManager($api);
        $this->expectException(get_class($exception));

        $manager->cancel($merchantId, $orderId);
    }

    /**
     * @return mixed[]
     */
    public function ordersProvider()
    {
        return [
            ['1', '1', 'Test'],
            ['2', '10', 'Success'],
            ['3', '100', 'Message'],
        ];
    }

    /**
     * @return mixed[]
     */
    public function exceptionsProvider()
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
                $item[2] = $exception;
                $result[] = $item;
            }
        }

        return $result;
    }
}

