<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static RECEIVED()
 * @method static static ERROR()
 *
 * @psalm-immutable
 */
final class Status extends AbstractEnum
{
    const RECEIVED = 'received';
    const ERROR = 'error';
}
