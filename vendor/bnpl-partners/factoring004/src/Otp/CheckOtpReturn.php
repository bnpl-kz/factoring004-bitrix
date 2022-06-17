<?php

declare(strict_types=1);

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

    public function __construct(int $amountAr, string $merchantId, string $merchantOrderId, string $otp)
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
     */
    public static function createFromArray($checkOtpReturn): CheckOtpReturn
    {
        return new self($checkOtpReturn['amountAR'], $checkOtpReturn['merchantId'], $checkOtpReturn['merchantOrderId'], $checkOtpReturn['otp']);
    }

    public function getAmountAr(): int
    {
        return $this->amountAr;
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
     * @psalm-return array{amountAR: int, merchantId: string, merchantOrderId: string, otp: string}
     */
    public function toArray(): array
    {
        return [
            'amountAR' => $this->getAmountAr(),
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'otp' => $this->getOtp(),
        ];
    }
}
