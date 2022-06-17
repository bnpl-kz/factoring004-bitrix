<?php

declare(strict_types=1);

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

    public function __construct(int $amountAr, string $merchantId, string $merchantOrderId)
    {
        $this->amountAr = $amountAr;
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @param array<string, mixed> $sendOtpReturn
     * @psalm-param array{amountAR: int, merchantId: string, merchantOrderId: string} $sendOtpReturn
     */
    public static function createFromArray($sendOtpReturn): SendOtpReturn
    {
        return new self($sendOtpReturn['amountAR'], $sendOtpReturn['merchantId'], $sendOtpReturn['merchantOrderId']);
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

    /**
     * @psalm-return array{amountAR: int, merchantId: string, merchantOrderId: string}
     */
    public function toArray(): array
    {
        return [
            'amountAR' => $this->getAmountAr(),
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
        ];
    }
}
