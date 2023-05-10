<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\Otp\CheckOtp;
use BnplPartners\Factoring004\Otp\SendOtp;

class Delivery extends AbstractStatusConfirmation
{
    public function sendOtp(): StatusConfirmationResponse
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->sendOtp(
                new SendOtp($this->merchantId, $this->orderId, $this->amount)
            )->getMsg()
        );
    }

    public function checkOtp(string $otp): StatusConfirmationResponse
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->checkOtp(
                new CheckOtp($this->merchantId, $this->orderId, $otp, $this->amount)
            )->getMsg()
        );
    }

    protected function getMerchantOrder(): AbstractMerchantOrder
    {
        return new DeliveryOrder($this->orderId, DeliveryStatus::DELIVERED(), $this->amount);
    }
}
