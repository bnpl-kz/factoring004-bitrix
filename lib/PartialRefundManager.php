<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\ArgumentException;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Exception;
use InvalidArgumentException;

class PartialRefundManager extends AbstractManager
{
    /**
     * @param array<string, int> $items
     */
    public static function create(Order $order, array $items): PartialRefundManager
    {
        return new self($order, $items);
    }

    /**
     * @throws \Bnpl\Payment\PartialRefundManagerException
     */
    public function calculateAmount(): int
    {
        $amount = 0;

        foreach ($this->items as $itemId => $remainingQuantity) {
            try {
                $basketItem = $this->order->getBasket()->getItemById($itemId);

                if (!$basketItem) {
                    throw new PartialRefundManagerException("Basket item by id {$itemId} is not found");
                }

                $amount += $basketItem->getPrice() * $remainingQuantity;
            } catch (ArgumentException $e) {
                throw new PartialRefundManagerException('Could not calculate refund amount', 0, $e);
            }
        }

        if ($amount <= 0 || $amount >= $this->order->getPrice()) {
            throw new EmptyBasketItemsException('You are trying to refund all items. Please use full refund instead.');
        }

        return (int) ceil($this->order->getPrice() - $amount);
    }

    /**
     * @throws \Bnpl\Payment\EmptyBasketItemsException
     * @throws \Bnpl\Payment\PartialRefundManagerException
     */
    public function refund(): void
    {
        $basket = $this->order->getBasket();

        try {
            /** @var \Bitrix\Sale\BasketItem $basketItem */
            foreach ($basket->getBasketItems() as $i => $basketItem) {
                $remainingQuantity = $this->items[(string) $basketItem->getId()] ?? null;

                if ($remainingQuantity === null) {
                    continue;
                }

                if ($basketItem->getQuantity() - $remainingQuantity > 0) {
                    $basketItem->setFieldNoDemand('QUANTITY', $basketItem->getQuantity() - $remainingQuantity);
                } else {
                    $basket->deleteItem($i);
                }
            }

            if (!$basket->count()) {
                throw new EmptyBasketItemsException('You are trying to refund all items. Please use full refund instead.');
            }

            $payment = $this->findOrderPayment();
            $payment->setPaid('N');
            $payment->delete();

            $newPayment = $this->order->getPaymentCollection()->createItem($payment->getPaySystem());
            $newPayment->setField('SUM', $this->order->getPrice());
            $newPayment->setPaid('Y');

            $this->order->save();
        } catch (EmptyBasketItemsException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new PartialRefundManagerException('Could not partial refund', 0, $e);
        }
    }
}
