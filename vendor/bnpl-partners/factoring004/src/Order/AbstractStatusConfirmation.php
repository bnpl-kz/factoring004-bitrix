<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\Otp\OtpResource;

abstract class AbstractStatusConfirmation implements StatusConfirmationInterface
{
    /**
     * @var \BnplPartners\Factoring004\Otp\OtpResource
     */
    protected $otpResource;
    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource
     */
    protected $changeStatusResource;
    /**
     * @var string
     */
    protected $merchantId;
    /**
     * @var string
     */
    protected $orderId;
    /**
     * @var int
     */
    protected $amount;

    /**
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     */
    public function __construct(
        OtpResource $otpResource,
        ChangeStatusResource $changeStatusResource,
        $merchantId,
        $orderId,
        $amount
    ) {
        $this->otpResource = $otpResource;
        $this->changeStatusResource = $changeStatusResource;
        $this->merchantId = $merchantId;
        $this->orderId = $orderId;
        $this->amount = $amount;
    }

    /**
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public function confirmWithoutOtp()
    {
        return StatusConfirmationResponse::create(
            ChangeStatusCaller::create($this->changeStatusResource, $this->merchantId)
                ->call($this->getMerchantOrder())
                ->getMsg()
        );
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder
     */
    abstract protected function getMerchantOrder();
}
