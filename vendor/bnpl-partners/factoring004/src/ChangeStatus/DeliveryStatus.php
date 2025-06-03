<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static DELIVERY()
 * @method static static DELIVERED()
 *
 * @psalm-immutable
 */
final class DeliveryStatus extends AbstractEnum
{
    /**
     * @deprecated Use DeliveryStatus::DELIVERED instead
     */
    const DELIVERY = 'delivered';
    const DELIVERED = 'delivered';
}
