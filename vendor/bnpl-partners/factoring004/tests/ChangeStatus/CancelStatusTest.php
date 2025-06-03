<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class CancelStatusTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCancel()
    {
        $this->assertEquals(CancelStatus::CANCEL(), CancelStatus::from('canceled'));
    }
}
