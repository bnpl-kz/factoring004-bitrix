<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class ReturnStatusTest extends TestCase
{
    public function testRETURN(): void
    {
        $this->assertEquals(ReturnStatus::RETURN(), ReturnStatus::from('return'));
    }

    public function testPARTRETURN(): void
    {
        $this->assertEquals(ReturnStatus::PARTRETURN(), ReturnStatus::from('part_return'));
    }
}

