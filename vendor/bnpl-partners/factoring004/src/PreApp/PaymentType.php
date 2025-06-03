<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static BNPL_004()
 * @method static static PAD()
 *
 * @psalm-immutable
 */
final class PaymentType extends AbstractEnum
{
    const BNPL_004 = '0-0-4';
    const PAD = 'PAD';

    public static function defaultValue()
    {
        return self::BNPL_004();
    }
}