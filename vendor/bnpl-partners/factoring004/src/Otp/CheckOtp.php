<?php

declare(strict_types=1);

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

    public function __construct(string $merchantId, string $merchantOrderId, string $otp, int $amount)
    {
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
        $this->otp = $otp;
        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $checkOtp
     * @psalm-param array{merchantId: string, merchantOrderId: string, otp: string, amount: int} $checkOtp
     */
    public static function createFromArray($checkOtp): CheckOtp
    {
        return new self($checkOtp['merchantId'], $checkOtp['merchantOrderId'], $checkOtp['otp'], $checkOtp['amount']);
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getMerchantOrderId(): string
    {
        return $this->merchantOrderId;
    }

    public function getOtp(): string
    {
        return $this->otp;
    }

    /**
     * @psalm-return array{merchantId: string, merchantOrderId: string, otp: string}
     */
    public function toArray(): array
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'otp' => $this->getOtp(),
            'amount' => $this->getAmount()
        ];
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
