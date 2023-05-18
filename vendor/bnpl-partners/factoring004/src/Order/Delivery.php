<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\Otp\CheckOtp;
use BnplPartners\Factoring004\Otp\SendOtp;

class Delivery extends AbstractStatusConfirmation
{
    /**
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public function sendOtp()
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->sendOtp(
                new SendOtp($this->merchantId, $this->orderId, $this->amount)
            )->getMsg()
        );
    }

    /**
     * @param string $otp
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public function checkOtp($otp)
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->checkOtp(
                new CheckOtp($this->merchantId, $this->orderId, $otp, $this->amount)
            )->getMsg()
        );
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder
     */
    protected function getMerchantOrder()
    {
        return new DeliveryOrder($this->orderId, DeliveryStatus::DELIVERED(), $this->amount);
    }
}
