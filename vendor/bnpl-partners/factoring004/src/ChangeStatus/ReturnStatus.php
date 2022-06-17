<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use MyCLabs\Enum\Enum;

/**
 * @method static static RETURN()
 * @method static static PARTRETURN()
 *
 * @psalm-immutable
 */
final class ReturnStatus extends Enum
{
    const RETURN = 'return';
    const PARTRETURN = 'part_return';
}
