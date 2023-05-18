<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\Otp\CheckOtpReturn;
use BnplPartners\Factoring004\Otp\SendOtpReturn;

class PartialRefund extends AbstractStatusConfirmation
{
    /**
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public function sendOtp()
    {
        return StatusConfirmationResponse::create(
            $this->otpResource->sendOtpReturn(
                new SendOtpReturn($this->amount, $this->merchantId, $this->orderId)
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
            $this->otpResource->checkOtpReturn(
                new CheckOtpReturn($this->amount, $this->merchantId, $this->orderId, $otp)
            )->getMsg()
        );
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder
     */
    protected function getMerchantOrder()
    {
        return new ReturnOrder($this->orderId, ReturnStatus::PARTRETURN(), $this->amount);
    }
}
