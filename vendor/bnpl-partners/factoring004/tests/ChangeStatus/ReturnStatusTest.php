<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractTestCase;

class ReturnStatusTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testRETURN()
    {
        $this->assertEquals(ReturnStatus::RE_TURN(), ReturnStatus::from('return'));
    }

    /**
     * @return void
     */
    public function testPARTRETURN()
    {
        $this->assertEquals(ReturnStatus::PARTRETURN(), ReturnStatus::from('part_return'));
    }
}

