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
}