<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\ArrayInterface;

class MerchantsOrders implements ArrayInterface
{
    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder[]
     */
    private $orders;

    /**
     * @param \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder[] $orders
     */
    public function __construct(string $merchantId, array $orders)
    {
        $this->merchantId = $merchantId;
        $this->orders = $orders;
    }

    /**
     * @param array<string, mixed> $merchantsOrders
     * @psalm-param array{merchantId: string, orders: array{orderId: string, status: string, amount?: int}[]} $merchantsOrders
     */
    public static function createFromArray($merchantsOrders): MerchantsOrders
    {
        return new self(
            $merchantsOrders['merchantId'],
            array_map(function (array $order) {
                if (!array_key_exists('amount', $order)) {
                    return CancelOrder::createFromArray($order);
                }

                if (ReturnStatus::search($order['status']) !== false) {
                    return ReturnOrder::createFromArray($order);
                }

                return DeliveryOrder::createFromArray($order);
            }, $merchantsOrders['orders'])
        );
    }

    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @return \BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * @psalm-return array{merchantId: string, orders: array{orderId: string, status: string, amount?: int}[]}
     */
    public function toArray(): array
    {
        return [
            'merchantId' => $this->getMerchantId(),
            'orders' => array_map(function (AbstractMerchantOrder $order) {
                return $order->toArray();
            }, $this->getOrders()),
        ];
    }
}
