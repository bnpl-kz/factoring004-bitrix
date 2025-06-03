<?php

namespace BnplPartners\Factoring004\ChangeStatus;

/**
 * @extends AbstractMerchantOrder<CancelStatus>
 */
class CancelOrder extends AbstractMerchantOrder
{

    /**
     * @param string $orderId
     */
    public function __construct($orderId, CancelStatus $status)
    {
        parent::__construct($orderId, $status);
    }

    /**
     * @param array<string, mixed> $order
     * @psalm-param array{orderId: string, status: string} $order
     * @return \BnplPartners\Factoring004\ChangeStatus\CancelOrder
     */
    public static function createFromArray(array $order)
    {
        return new self($order['orderId'], new CancelStatus($order['status']));
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\CancelStatus
     */
    public function getStatus()
    {
        return $this->status;
    }
}
