<?php

namespace BnplPartners\Factoring004;

use MyCLabs\Enum\Enum;

/**
 * @method static static from(string $value)
 */
abstract class AbstractEnum extends Enum
{
    /**
     * @param string $name
     * @param array $arguments
     *
     * @return \BnplPartners\Factoring004\AbstractEnum
     */
    public static function __callStatic($name, $arguments)
    {
        if ($name === 'from') {
            return parent::__callStatic(static::search(...$arguments), []);
        }

        return parent::__callStatic($name, $arguments);
    }
}
