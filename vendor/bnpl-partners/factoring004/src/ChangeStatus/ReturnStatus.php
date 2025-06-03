<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static RETURN()
 * @method static static PARTRETURN()
 *
 * @psalm-immutable
 */
final class ReturnStatus extends AbstractEnum
{
    const RE_TURN = 'return';
    const PARTRETURN = 'part_return';

    public static function __callStatic($name, $arguments)
    {
        if ($name === "RETURN") {
            $name = "RE_TURN";
        }

        return parent::__callStatic($name, $arguments);
    }
}
