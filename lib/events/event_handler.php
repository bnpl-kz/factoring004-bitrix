<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Sale\Order;

class EventHandler {

    private $mixSum = 6000;
    private $maxSum = 200000;

    public function hidePaySystem
    (
        Order $order,
        array &$arUserResult,
        HttpRequest $request,
        array &$arParams,
        array &$arResult,
        array &$arDeliveryServiceAll,
        &$arPaySystemServiceAll): void
    {
        if ($order->getPrice() < $this->mixSum || $order->getPrice() > $this->maxSum) {
            $index = $this->getPaymentSystemIndex($arPaySystemServiceAll);
            if ($index) {
                unset($arPaySystemServiceAll[$index]);
            }
        }
    }


    private function getPaymentSystemIndex(array $paymentSystems): ?int
    {
        foreach ($paymentSystems as $i => $item) {
            if ($item['NAME'] === 'BNPLPayment') {
                return $i;
            }
        }
        return null;
    }
}
