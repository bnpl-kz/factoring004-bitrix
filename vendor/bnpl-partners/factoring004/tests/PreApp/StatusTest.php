<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractTestCase;

class StatusTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testRECEIVED()
    {
        $this->assertEquals(Status::RECEIVED(), Status::from('received'));
    }

    /**
     * @return void
     */
    public function testERROR()
    {
        $this->assertEquals(Status::ERROR(), Status::from('error'));
    }
}

