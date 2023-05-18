<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\ArrayInterface;

/**
 * @psalm-immutable
 */
class CheckOtpReturn implements ArrayInterface
{
    /**
     * @var int
     */
    private $amountAr;
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
     * @param int $amountAr
     * @param string $merchantId
     * @param string $merchantOrderId
     * @param string $otp
     */
    public function __construct($amountAr, $merchantId, $merchantOrderId, $otp)
    {
        $this->amountAr = $amountAr;
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
        $this->otp = $otp;
    }

    /**
     * @param array<string, mixed> $checkOtpReturn
     *
     * @psalm-param array{amountAR: int, merchantId: string, merchantOrderId: string, otp: string} $checkOtpReturn
     * @return \BnplPartners\Factoring004\Otp\CheckOtpReturn
     */
    public static function createFromArray(array $checkOtpReturn)
    {
        return new self($checkOtpReturn['amountAR'], $checkOtpReturn['merchantId'], $checkOtpReturn['merchantOrderId'], $checkOtpReturn['otp']);
    }

    /**
     * @return int
     */
    public function getAmountAr()
    {
        return $this->amountAr;
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
     * @psalm-return array{amountAR: int, merchantId: string, merchantOrderId: string, otp: string}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'amountAR' => $this->getAmountAr(),
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'otp' => $this->getOtp(),
        ];
    }
}
