<?php

namespace Bnpl\PaymentPad;

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

    public function updateOrder()
    {
        $basket = $this->order->getBasket();

        /** @var \Bitrix\Sale\BasketItem $basketItem */
        foreach ($basket->getBasketItems() as $i => $basketItem) {
            $deliveryQuantity = $this->items[(string) $basketItem->getId()] ?? 0;

            if ($deliveryQuantity > 0) {
                $basketItem->setFieldNoDemand('QUANTITY', $deliveryQuantity);
            } else {
                $basket->deleteItem($i);
            }
        }

        $payment = $this->findOrderPayment();
        $payment->setPaid('N');
        $payment->delete();

        $newPayment = $this->order->getPaymentCollection()->createItem($payment->getPaySystem());
        $newPayment->setField('SUM', $this->order->getPrice());
        $newPayment->setPaid('Y');

        $this->order->save();
    }
}