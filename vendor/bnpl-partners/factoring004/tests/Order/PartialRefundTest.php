<?php

declare(strict_types=1);

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
use PHPUnit\Framework\TestCase;

class PartialRefundTest extends TestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     */
    public function testSendOtp(string $merchantId, string $orderId, int $amount, string $message): void
    {
        $sendOtpReturn = new SendOtpReturn($amount, $merchantId, $orderId);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtpReturn')
            ->with($sendOtpReturn)
            ->willReturn(new DtoOtp($message));

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->sendOtp());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     */
    public function testCheckOtp(string $merchantId, string $orderId, int $amount, string $message, string $otp): void
    {
        $checkOtpReturn = new CheckOtpReturn($amount, $merchantId, $orderId, $otp);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtpReturn')
            ->with($checkOtpReturn)
            ->willReturn(new DtoOtp($message));

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->checkOtp($otp));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider dataProvider
     */
    public function testConfirmWithoutOtp(string $merchantId, string $orderId, int $amount, string $message): void
    {
        $orders = [
            new MerchantsOrders($merchantId, [new ReturnOrder($orderId, ReturnStatus::PARTRETURN(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willReturn(new ChangeStatusResponse([new SuccessResponse('', $message)], []));

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->assertEquals(new StatusConfirmationResponse($message), $refund->confirmWithoutOtp());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     */
    public function testSendOtpWithError(
        string $merchantId,
        string $orderId,
        int $amount,
        string $message,
        string $otp,
        PackageException $exception
    ): void {
        $sendOtpReturn = new SendOtpReturn($amount, $merchantId, $orderId);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('sendOtpReturn')
            ->with($sendOtpReturn)
            ->willThrowException($exception);

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);

        $this->expectException(get_class($exception));

        $refund->sendOtp();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     */
    public function testCheckOtpWithError(
        string $merchantId,
        string $orderId,
        int $amount,
        string $message,
        string $otp,
        PackageException $exception
    ): void {
        $checkOtpReturn = new CheckOtpReturn($amount, $merchantId, $orderId, $otp);

        $changeStatusResource = $this->createStub(ChangeStatusResource::class);
        $otpResource = $this->createMock(OtpResource::class);
        $otpResource->expects($this->once())
            ->method('checkOtpReturn')
            ->with($checkOtpReturn)
            ->willThrowException($exception);

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);
        $this->expectException(get_class($exception));

        $refund->checkOtp($otp);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     */
    public function testConfirmWithoutOtpWithError(
        string $merchantId,
        string $orderId,
        int $amount,
        string $message,
        string $otp,
        PackageException $exception
    ): void {
        $orders = [
            new MerchantsOrders($merchantId, [new ReturnOrder($orderId, ReturnStatus::PARTRETURN(), $amount)]),
        ];

        $otpResource = $this->createStub(OtpResource::class);
        $changeStatusResource = $this->createMock(ChangeStatusResource::class);
        $changeStatusResource->expects($this->once())
            ->method('changeStatusJson')
            ->with($orders)
            ->willThrowException($exception);

        $refund = new PartialRefund($otpResource, $changeStatusResource, $merchantId, $orderId, $amount);
        $this->expectException(get_class($exception));

        $refund->confirmWithoutOtp();
    }

    public function dataProvider(): array
    {
        return [
            ['1', '1', 6000, 'ok', '1234'],
            ['10', '1000', 8000, 'test', '0204'],
            ['100', '10', 10_000, 'message', '0000'],
        ];
    }

    public function exceptionsProvider(): array
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
                $result[] = [...$item, $exception];
            }
        }

        return $result;
    }
}
