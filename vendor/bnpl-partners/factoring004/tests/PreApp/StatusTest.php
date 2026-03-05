<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testRECEIVED(): void
    {
        $this->assertEquals(Status::RECEIVED(), Status::from('received'));
    }

    public function testERROR(): void
    {
        $this->assertEquals(Status::ERROR(), Status::from('error'));
    }
}

