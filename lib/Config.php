<?php

namespace Bnpl\Payment;

use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Internals\PaySystemActionTable;
use Exception;

class Config
{
    const KEY_PREFIX = 'PAYSYSTEM_';

    /**
     * @var string|null
     */
    private static $paySystemId;

    /**
     * @param string|null $key
     *
     * @return string|null
     */
    public static function get($key)
    {
        try {
            return BusinessValue::get($key, static::KEY_PREFIX . static::findPaySystemId());
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public static function getPaySystemId()
    {
        try {
            return static::findPaySystemId();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return string|null
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function findPaySystemId()
    {
        if (static::$paySystemId) {
            return static::$paySystemId;
        }

        $result = PaySystemActionTable::getRow([
            'select' => array('ID'),
            'filter' => array('CODE' => 'factoring004', 'ACTIVE' => 'Y'),
            'limit' => 1,
        ]);

        return static::$paySystemId = isset($result) ? $result['ID'] : null;
    }

    public static function getDeliveryIds()
    {
        $paySysKey = static::findPaySystemId();
        if (!$paySysKey) {
            return [];
        }
        $result = array();
        $allDefaultValues = self::getDeliveryItems('');
        $allOverrideValues = self::getDeliveryItems('PAYSYSTEM_'.$paySysKey);

        foreach ($allDefaultValues as $id => $val) {

            if (isset($allOverrideValues[$id])) {
                if ($allOverrideValues[$id] === 'Y') {
                    $result[] = $id;
                    unset($allOverrideValues[$id]);
                }
            } elseif($val === 'Y') {
                $result[] = $id;
            }

        }
        $result = array_merge($result,array_filter($allOverrideValues, function ($value) {
            return $value === 'Y';
        }));
        return $result;
    }


    private static function getDeliveryItems($prefix)
    {
        $result = array();
        if (!isset(BusinessValue::getConsumerCodePersonMapping()[$prefix])) {
            return [];
        }
        foreach (BusinessValue::getConsumerCodePersonMapping()[$prefix] as $key => $item) {
            if (strpos($key,'BNPL_PAYMENT_DELIVERY_') !== false) {
                foreach ($item as $val) {
                    $result[substr($key, strlen('BNPL_PAYMENT_DELIVERY_'))] = $val['PROVIDER_VALUE'];
                }
            }
        }
        return $result;
    }
}
