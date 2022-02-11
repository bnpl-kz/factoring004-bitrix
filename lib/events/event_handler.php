<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Internals\BusinessValuePersonDomainTable;
use Bitrix\Sale\Order;

class EventHandler
{
    const MIN_SUM = 6000;
    const MAX_SUM = 200000;
    const REQUIRED_OPTIONS = [
        'BNPL_PAYMENT_CONSUMER_KEY',
        'BNPL_PAYMENT_CONSUMER_SECRET',
        'BNPL_PAYMENT_API_HOST',
        'BNPL_PAYMENT_PARTNER_NAME',
        'BNPL_PAYMENT_PARTNER_CODE',
        'BNPL_PAYMENT_POINT_CODE',
    ];

    public static function hidePaySystem(
        Order $order,
        array &$arUserResult,
        HttpRequest $request,
        array &$arParams,
        array &$arResult,
        array &$arDeliveryServiceAll,
        &$arPaySystemServiceAll
    ) {
        if (!static::isRequiredOptionsFilled()) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
            return;
        }

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

    /**
     * @return bool
     */
    private static function isRequiredOptionsFilled()
    {
        foreach (static::REQUIRED_OPTIONS as $option) {
            if (!BusinessValue::get($option, 'bnpl.payment')) {
                return false;
            }
        }

        return true;
    }
}
