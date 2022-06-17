<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\ArrayInterface;
use MyCLabs\Enum\Enum;

/**
 * @template T
 */
abstract class AbstractMerchantOrder implements ArrayInterface
{
    /**
     * @var string
     */
    protected $orderId;

    /**
     * @var T
     */
    protected $status;

    /**
     * @param string $orderId
     * @param T $status
     */
    public function __construct(string $orderId, Enum $status)
    {
        $this->orderId = $orderId;
        $this->status = $status;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @codeCoverageIgnore
     * @return T
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @psalm-return array{orderId: string, status: string}
     */
    public function toArray(): array
    {
        return [
            'orderId' => $this->getOrderId(),
            'status' => (string) $this->getStatus()->getValue(),
        ];
    }
}
