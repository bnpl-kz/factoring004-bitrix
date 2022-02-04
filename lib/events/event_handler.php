<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Sale\Order;

class EventHandler
{
    private const MIN_SUM = 6000;
    private const MAX_SUM = 200000;

    public static function hidePaySystem(
        Order $order,
        array &$arUserResult,
        HttpRequest $request,
        array &$arParams,
        array &$arResult,
        array &$arDeliveryServiceAll,
        &$arPaySystemServiceAll
    ): void {
        if ($order->getPrice() < static::MIN_SUM || $order->getPrice() > static::MAX_SUM) {
            $index = static::getPaymentSystemIndex($arPaySystemServiceAll);
            if ($index) {
                unset($arPaySystemServiceAll[$index]);
            }
        }
    }

    private static function getPaymentSystemIndex(array $paymentSystems): ?int
    {
        foreach ($paymentSystems as $i => $item) {
            if ($item['NAME'] === 'BNPLPayment') {
                return $i;
            }
        }
        return null;
    }
}
