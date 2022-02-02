<?php

namespace Bnpl\Payment;

use Bitrix\Main\Type\Datetime;
use Bitrix\Main\Entity;

class OrdersTable extends Entity\DataManager
{

    private static $table_name = 'bnpl_payment_orders';

    public function __construct()
    {
        //
    }

    public static function getTableName()
    {
        return self::$table_name;
    }

//    public static function getConnectionName()
//    {
//        return 'default';
//    }


    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('ORDER_ID', array(
                'required' => true
            )),
            new Entity\StringField('STATUS', array(
                'required' => true,
                'default_value' => 'pending'
            )),
            new Entity\DatetimeField('CREATED_AT', [
                'default_value'=>function() {
                    return Datetime::createFromPhp(new \Datetime());
                }
            ]),
            new Entity\DatetimeField('UPDATED_AT', [
                'default_value'=>function() {
                    return Datetime::createFromPhp(new \Datetime());
                }
            ])
        );
    }
}