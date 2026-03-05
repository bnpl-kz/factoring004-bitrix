<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class NoAuthTest extends TestCase
{
    public function testApply(): void
    {
        $auth = new NoAuth();
        $expected = new Request('GET', '/');

        $request = $auth->apply($expected);

        $this->assertEquals($expected, $request);
    }
}

