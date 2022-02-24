<?php

namespace Bnpl\Payment;

use App\Http\Controllers\Controller;
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
            'filter' => array('NAME' => 'BNPLPayment'),
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
        $all = BusinessValue::getConsumerCodePersonMapping()['PAYSYSTEM_'.$paySysKey];
        foreach ($all as $key => $item) {
            if (strpos($key,'BNPL_PAYMENT_DELIVERY_') !== false) {
                foreach ($item as $val) {
                    if ($val['PROVIDER_VALUE'] === 'Y') {
                        $result[] = substr($key, strlen('BNPL_PAYMENT_DELIVERY_'));
                    }
                }
            }
        }
        return $result;
    }
}
