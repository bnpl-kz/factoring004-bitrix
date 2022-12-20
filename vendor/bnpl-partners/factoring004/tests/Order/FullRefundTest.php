<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse;
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
use BnplPartners\Factoring004\Otp\CheckOtpReturn;
use BnplPartners\Factoring004\Otp\DtoOtp;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\Otp\SendOtpReturn;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;
use BnplPartners\Factoring004\AbstractTestCase;

class FullRefundTest extends AbstractTestCase
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
        $sendOtpReturn = new SendOtpReturn($amount, $merchantId, $orderId);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtpReturn')
            ->with($sendOtpReturn)
            ->willReturn(new DtoOtp($message));

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->sendOtp());
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
        $checkOtpReturn = new CheckOtpReturn($amount, $merchantId, $orderId, $otp);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtpReturn')
            ->with($checkOtpReturn)
            ->willReturn(new DtoOtp($message));

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->checkOtp($otp));
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
            new MerchantsOrders($merchantId, [new ReturnOrder($orderId, ReturnStatus::RE_TURN(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([new SuccessResponse('', $message)], []));

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->confirmWithoutOtp());
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
        $sendOtpReturn = new SendOtpReturn($amount, $merchantId, $orderId);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtpReturn')
            ->with($sendOtpReturn)
            ->willThrowException($exception);

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);

        $this->expectException(get_class($exception));

        $refund->sendOtp();
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
        $checkOtpReturn = new CheckOtpReturn($amount, $merchantId, $orderId, $otp);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtpReturn')
            ->with($checkOtpReturn)
            ->willThrowException($exception);

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);
        $this->expectException(get_class($exception));

        $refund->checkOtp($otp);
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
            new MerchantsOrders($merchantId, [new ReturnOrder($orderId, ReturnStatus::RE_TURN(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willThrowException($exception);

        $refund = new FullRefund($otpResource, $changeStatusResource, $merchantId, $orderId);
        $this->expectException(get_class($exception));

        $refund->confirmWithoutOtp();
    }

    /**
     * @return mixed[]
     */
    public function dataProvider()
    {
        return [
            ['1', '1', 0, 'ok', '1234'],
            ['10', '1000', 0, 'test', '0204'],
            ['100', '10', 0, 'message', '0000'],
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
