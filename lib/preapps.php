<?php

namespace Bnpl\Payment;

use Bitrix\Main\Entity;

class PreappsTable extends Entity\DataManager
{

    private static $table_name = 'bnpl_payment_order_preapps';

    public function __construct()
    {
        //
    }

    public static function getTableName()
    {
        return self::$table_name;
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\StringField('PREAPP_UID', array(
                'required' => true
            )),
            new Entity\IntegerField('ORDER_ID',array(
                'required'=>true
            )),
            new Entity\ReferenceField(
                'ORDER',
                '\Bnpl\Payment\PreappsTable',
                array('=this.ORDER_ID','ref.ID')
            )
        );
    }
}
