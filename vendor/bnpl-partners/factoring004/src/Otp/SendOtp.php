<?php

declare(strict_types=1);

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

    public function __construct(string $merchantId, string $merchantOrderId, int $amount)
    {
        $this->merchantId = $merchantId;
        $this->merchantOrderId = $merchantOrderId;
        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $sendOtp
     *
     * @psalm-param array{merchantId: string, merchantOrderId: string, amount: int} $sendOtp
     */
    public static function createFromArray($sendOtp): SendOtp
    {
        return new self($sendOtp['merchantId'], $sendOtp['merchantOrderId'], $sendOtp['amount']);
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    public function getMerchantOrderId(): string
    {
        return $this->merchantOrderId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @psalm-return array{merchantId: string, merchantOrderId: string, amount: int}
     */
    public function toArray(): array
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'merchantOrderId' => $this->getMerchantOrderId(),
            'amount' => $this->getAmount()
        ];
    }
}
