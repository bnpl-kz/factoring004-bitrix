<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\ArrayInterface;

/**
 * @psalm-immutable
 */
class CheckOtp implements ArrayInterface
{
    /**
     * @var string
     */
    private $merchantId;
    /**
     * @var string
     */
    private $merchantOrderId;
    /**
     * @var string
     */
    private $otp;
    /**
     * @var int
     */
    private $amount;

    /**
     * @param string $merchantId
     * @param string $merchantOrderId
     * @param string $otp
     * @param int $amount
     */
    public function __construct($merchantId, $merchantOrderId, $otp, $amount)
    {
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
        $this->otp = $otp;
        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $checkOtp
     * @psalm-param array{merchantId: string, merchantOrderId: string, otp: string, amount: int} $checkOtp
     * @return \BnplPartners\Factoring004\Otp\CheckOtp
     */
    public static function createFromArray(array $checkOtp)
    {
        return new self($checkOtp['merchantId'], $checkOtp['merchantOrderId'], $checkOtp['otp'], $checkOtp['amount']);
    }

    /**
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @return string
     */
    public function getOtp()
    {
        return $this->otp;
    }

    /**
     * @psalm-return array{merchantId: string, merchantOrderId: string, otp: string}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'otp' => $this->getOtp(),
            'amount' => $this->getAmount()
        ];
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
