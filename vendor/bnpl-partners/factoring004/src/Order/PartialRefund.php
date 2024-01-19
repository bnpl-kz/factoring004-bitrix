<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\Otp\CheckOtpReturn;
use BnplPartners\Factoring004\Otp\SendOtpReturn;

class PartialRefund extends AbstractStatusConfirmation
{
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
        return new ReturnOrder($this->orderId, ReturnStatus::PARTRETURN(), $this->amount);
    }
}
