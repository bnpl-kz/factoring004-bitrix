<?php

namespace Bnpl\Payment;

use Bitrix\Main\ArgumentException;
use Bitrix\Sale\Order;

class DeliveryManager extends AbstractManager
{

    public static function create(Order $order, array $items) : DeliveryManager
    {
        return new self($order, $items);
    }

    public function calculateAmount(): int
    {
        $amount = 0;

        foreach ($this->items as $itemId => $deliveryQuantity) {
            try {
                $basketItem = $this->order->getBasket()->getItemById($itemId);

                if (!$basketItem) {
                    throw new DeliveryManagerException("Basket item by id {$itemId} is not found");
                }

                $amount += $basketItem->getPrice() * $deliveryQuantity;
            } catch (ArgumentException $e) {
                throw new DeliveryManagerException('Could not calculate delivery amount', 0, $e);
            }
        }

        $amount = $this->order->getSumPaid() - $this->order->getBasket()->getPrice() + $amount;

        if ($amount <= 0) {
            throw new DeliveryAmountException('You are trying to delivery nothing. Use cancel if you want cancel order');
        }
        if ($amount > $this->order->getPrice()) {
            throw new DeliveryAmountException('Delivery amount cant be more then order amount');
        }

        return (int) ceil($amount);
    }
}