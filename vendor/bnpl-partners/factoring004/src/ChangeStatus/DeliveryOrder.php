<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

/**
 * @extends AbstractMerchantOrder<DeliveryStatus>
 */
class DeliveryOrder extends AbstractMerchantOrder
{
    /**
     * @var int
     */
    private $amount;

    public function __construct(string $orderId, DeliveryStatus $status, int $amount)
    {
        parent::__construct($orderId, $status);

        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $order
     * @psalm-param array{orderId: string, status: string, amount: int} $order
     */
    public static function createFromArray($order): DeliveryOrder
    {
        return new self($order['orderId'], new DeliveryStatus($order['status']), $order['amount']);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @psalm-return array{orderId: string, status: string, amount: int}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'amount' => $this->getAmount(),
        ]);
    }
}
