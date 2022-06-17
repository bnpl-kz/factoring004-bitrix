<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use MyCLabs\Enum\Enum;

/**
 * @method static static DELIVERY()
 * @method static static DELIVERED()
 *
 * @psalm-immutable
 */
final class DeliveryStatus extends Enum
{
    /**
     * @deprecated Use DeliveryStatus::DELIVERED instead
     */
    const DELIVERY = 'delivered';
    const DELIVERED = 'delivered';
}
