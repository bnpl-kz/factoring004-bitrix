<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Sale\Internals\BusinessValuePersonDomainTable;
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
    ) {
        if (!static::isIndividualPersonType($arUserResult['PERSON_TYPE_ID'])) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
        }

        if ($order->getPrice() < static::MIN_SUM || $order->getPrice() > static::MAX_SUM) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
        }
    }

    private static function getPaymentSystemIndex(array $paymentSystems)
    {
        foreach ($paymentSystems as $i => $item) {
            if ($item['NAME'] === 'BNPLPayment') {
                return $i;
            }
        }
        return null;
    }

    private static function isIndividualPersonType($personTypeId)
    {
        return (bool) BusinessValuePersonDomainTable::getCount([
            'PERSON_TYPE_ID' => $personTypeId,
            'DOMAIN' => 'I',
        ]);
    }

    private static function disablePaymentSystemIfEnabled(array &$paymentSystems)
    {
        $index = static::getPaymentSystemIndex($paymentSystems);

        if ($index) {
            unset($paymentSystems[$index]);
        }
    }
}
