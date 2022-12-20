<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\ArrayInterface;

/**
 * @psalm-immutable
 */
class SendOtpReturn implements ArrayInterface
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
     * @param int $amountAr
     * @param string $merchantId
     * @param string $merchantOrderId
     */
    public function __construct($amountAr, $merchantId, $merchantOrderId)
    {
        $this->amountAr = $amountAr;
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @param array<string, mixed> $sendOtpReturn
     * @psalm-param array{amountAR: int, merchantId: string, merchantOrderId: string} $sendOtpReturn
     * @return \BnplPartners\Factoring004\Otp\SendOtpReturn
     */
    public static function createFromArray(array $sendOtpReturn)
    {
        return new self($sendOtpReturn['amountAR'], $sendOtpReturn['merchantId'], $sendOtpReturn['merchantOrderId']);
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
     * @psalm-return array{amountAR: int, merchantId: string, merchantOrderId: string}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'amountAR' => $this->getAmountAr(),
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
        ];
    }
}
