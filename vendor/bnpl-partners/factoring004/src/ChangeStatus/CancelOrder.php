<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

/**
 * @extends AbstractMerchantOrder<CancelStatus>
 */
class CancelOrder extends AbstractMerchantOrder
{

    public function __construct(string $orderId, CancelStatus $status)
    {
        parent::__construct($orderId, $status);
    }

    /**
     * @param array<string, mixed> $order
     * @psalm-param array{orderId: string, status: string} $order
     */
    public static function createFromArray(array $order): CancelOrder
    {
        return new self($order['orderId'], new CancelStatus($order['status']));
    }
}