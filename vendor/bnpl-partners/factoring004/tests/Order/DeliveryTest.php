<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
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
use BnplPartners\Factoring004\Otp\CheckOtp;
use BnplPartners\Factoring004\Otp\DtoOtp;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\Otp\SendOtp;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;
use BnplPartners\Factoring004\AbstractTestCase;

class DeliveryTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     */
    public function testSendOtp($merchantId, $orderId, $amount, $message)
    {
        $sendOtp = new SendOtp($merchantId, $orderId, $amount);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtp')
            ->with($sendOtp)
            ->willReturn(new DtoOtp($message));

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $delivery->sendOtp());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     * @param string $otp
     */
    public function testCheckOtp($merchantId, $orderId, $amount, $message, $otp)
    {
        $checkOtp = new CheckOtp($merchantId, $orderId, $otp, $amount);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtp')
            ->with($checkOtp)
            ->willReturn(new DtoOtp($message));

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $delivery->checkOtp($otp));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     */
    public function testConfirmWithoutOtp($merchantId, $orderId, $amount, $message)
    {
        $orders = [
            new MerchantsOrders($merchantId, [new DeliveryOrder($orderId, DeliveryStatus::DELIVERED(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([new SuccessResponse('', $message)], []));

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $delivery->confirmWithoutOtp());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     * @param string $otp
     */
    public function testSendOtpWithError(
        $merchantId,
        $orderId,
        $amount,
        $message,
        $otp,
        PackageException $exception
    ) {
        $sendOtp = new SendOtp($merchantId, $orderId, $amount);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtp')
            ->with($sendOtp)
            ->willThrowException($exception);

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->expectException(get_class($exception));

        $delivery->sendOtp();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     * @param string $otp
     */
    public function testCheckOtpWithError(
        $merchantId,
        $orderId,
        $amount,
        $message,
        $otp,
        PackageException $exception
    ) {
        $checkOtp = new CheckOtp($merchantId, $orderId, $otp, $amount);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtp')
            ->with($checkOtp)
            ->willThrowException($exception);

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);
        $this->expectException(get_class($exception));

        $delivery->checkOtp($otp);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     * @return void
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @param string $message
     * @param string $otp
     */
    public function testConfirmWithoutOtpWithError(
        $merchantId,
        $orderId,
        $amount,
        $message,
        $otp,
        PackageException $exception
    ) {
        $orders = [
            new MerchantsOrders($merchantId, [new DeliveryOrder($orderId, DeliveryStatus::DELIVERED(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willThrowException($exception);

        $delivery = new Delivery($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);
        $this->expectException(get_class($exception));

        $delivery->confirmWithoutOtp();
    }

    /**
     * @return mixed[]
     */
    public function dataProvider()
    {
        return [
            ['1', '1', 6000, 'ok', '1234'],
            ['10', '1000', 8000, 'test', '0204'],
            ['100', '10', 10000, 'message', '0000'],
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

        foreach ($this->dataProvider() as $item) {
            foreach ($exceptions as $exception) {
                $result[] = array_merge(is_array($item) ? $item : iterator_to_array($item), [$exception]);
            }
        }

        return $result;
    }
}

