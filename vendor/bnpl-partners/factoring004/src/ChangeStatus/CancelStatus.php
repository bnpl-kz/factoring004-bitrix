<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static CANCEL()
 *
 * @psalm-immutable
 */
final class CancelStatus extends AbstractEnum
{
    const CANCEL = 'canceled';
}
