<?php

namespace Bnpl\Payment;

use Bitrix\Sale\Order;

abstract class AbstractManager
{
    /**
     * @var \Bitrix\Sale\Order
     */
    protected $order;

    /**
     * @var array<string, int>
     */
    protected $items;

    /**
     * @param array<string, int> $items
     */
    public function __construct(Order $order, array $items)
    {
        $this->order = $order;
        $this->items = $items;
    }

    protected function findOrderPayment(): \Bitrix\Sale\Payment
    {
        /** @var \Bitrix\Sale\Payment $payment */
        foreach ($this->order->getPaymentCollection() as $payment) {
            $paySystemService = $payment->getPaySystem();

            if ($paySystemService->getField('CODE') === 'factoring004') {
                return $payment;
            }
        }

        throw new InvalidArgumentException('Payment by code factoring004 is not found');
    }
}