<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\Otp\OtpResource;

abstract class AbstractStatusConfirmation implements StatusConfirmationInterface
{
    protected OtpResource $otpResource;
    protected ChangeStatusResource $changeStatusResource;
    protected string $merchantId;
    protected string $orderId;
    protected int $amount;

    public function __construct(
        OtpResource $otpResource,
        ChangeStatusResource $changeStatusResource,
        string $merchantId,
        string $orderId,
        int $amount
    ) {
        $this->otpResource = $otpResource;
        $this->changeStatusResource = $changeStatusResource;
        $this->merchantId = $merchantId;
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    public function confirmWithoutOtp(): StatusConfirmationResponse
    {
        return StatusConfirmationResponse::create(
            ChangeStatusCaller::create($this->changeStatusResource, $this->merchantId)
                ->call($this->getMerchantOrder())
                ->getMsg()
        );
    }

    abstract protected function getMerchantOrder(): AbstractMerchantOrder;
}
