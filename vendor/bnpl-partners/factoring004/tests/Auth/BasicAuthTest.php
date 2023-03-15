<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class BasicAuthTest extends TestCase
{
    public function testApply(): void
    {
        $auth = new BasicAuth('test', 'test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('Basic ' . base64_encode('test:test'), $request->getHeaderLine('Authorization'));
    }
}

