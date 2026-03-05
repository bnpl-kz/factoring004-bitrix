<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use PHPUnit\Framework\TestCase;

class CancelStatusTest extends TestCase
{
    public function testCancel(): void
    {
        $this->assertEquals(CancelStatus::CANCEL(), CancelStatus::from('canceled'));
    }
}
