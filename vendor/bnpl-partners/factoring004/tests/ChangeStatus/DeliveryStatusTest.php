<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class DeliveryStatusTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testDELIVERY()
    {
        $this->assertEquals(DeliveryStatus::DELIVERY(), DeliveryStatus::from('delivered'));
    }

    /**
     * @return void
     */
    public function testDELIVERED()
    {
        $this->assertEquals(DeliveryStatus::DELIVERED(), DeliveryStatus::from('delivered'));
    }
}

