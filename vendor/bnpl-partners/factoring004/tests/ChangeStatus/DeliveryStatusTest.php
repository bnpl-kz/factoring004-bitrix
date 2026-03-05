<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class DeliveryStatusTest extends TestCase
{
    public function testDELIVERY(): void
    {
        $this->assertEquals(DeliveryStatus::DELIVERY(), DeliveryStatus::from('delivered'));
    }

    public function testDELIVERED(): void
    {
        $this->assertEquals(DeliveryStatus::DELIVERED(), DeliveryStatus::from('delivered'));
    }
}

