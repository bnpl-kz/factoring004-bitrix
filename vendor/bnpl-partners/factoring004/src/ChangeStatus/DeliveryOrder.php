<?php

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

    /**
     * @param string $orderId
     * @param int $amount
     */
    public function __construct($orderId, DeliveryStatus $status, $amount)
    {
        parent::__construct($orderId, $status);

        $this->amount = $amount;
    }

    /**
     * @param array<string, mixed> $order
     * @psalm-param array{orderId: string, status: string, amount: int} $order
     * @return \BnplPartners\Factoring004\ChangeStatus\DeliveryOrder
     */
    public static function createFromArray(array $order)
    {
        return new self($order['orderId'], new DeliveryStatus($order['status']), $order['amount']);
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\DeliveryStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @psalm-return array{orderId: string, status: string, amount: int}
     * @return mixed[]
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'amount' => $this->getAmount(),
        ]);
    }
}
