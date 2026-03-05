<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\Otp\CheckOtpReturn;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\Otp\SendOtpReturn;

class FullRefund extends AbstractStatusConfirmation
{
    public function __construct(
        OtpResource $otpResource,
        ChangeStatusResource $changeStatusResource,
        string $merchantId,
        string $orderId
    ) {
        parent::__construct($otpResource, $changeStatusResource, $merchantId, $orderId, 0);
    }

    public function sendOtp(): StatusConfirmationResponse
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->sendOtpReturn(
                new SendOtpReturn($this->amount, $this->merchantId, $this->orderId)
            )->getMsg()
        );
    }

    public function checkOtp(string $otp): StatusConfirmationResponse
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->checkOtpReturn(
                new CheckOtpReturn($this->amount, $this->merchantId, $this->orderId, $otp)
            )->getMsg()
        );
    }

    protected function getMerchantOrder(): AbstractMerchantOrder
    {
        return new ReturnOrder($this->orderId, ReturnStatus::RETURN(), $this->amount);
    }
}
