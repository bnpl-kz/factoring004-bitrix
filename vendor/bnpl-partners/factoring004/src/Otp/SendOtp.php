<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\ArrayInterface;

/**
 * @psalm-immutable
 */
class SendOtp implements ArrayInterface
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
     * @var int
     */
    private $amount;

    /**
     * @param string $merchantId
     * @param string $merchantOrderId
     * @param int $amount
     */
    public function __construct($merchantId, $merchantOrderId, $amount)
    {
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $sendOtp
     *
     * @psalm-param array{merchantId: string, merchantOrderId: string, amount: int} $sendOtp
     * @return \BnplPartners\Factoring004\Otp\SendOtp
     */
    public static function createFromArray(array $sendOtp)
    {
        return new self($sendOtp['merchantId'], $sendOtp['merchantOrderId'], $sendOtp['amount']);
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
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @psalm-return array{merchantId: string, merchantOrderId: string, amount: int}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'amount' => $this->getAmount()
        ];
    }
}
