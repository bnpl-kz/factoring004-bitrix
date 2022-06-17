<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use MyCLabs\Enum\Enum;

/**
 * @method static static RECEIVED()
 * @method static static ERROR()
 *
 * @psalm-immutable
 */
final class Status extends Enum
{
    const RECEIVED = 'received';
    const ERROR = 'error';
}
