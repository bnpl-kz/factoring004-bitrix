<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class BearerTokenAuthTest extends TestCase
{
    public function testApply(): void
    {
        $auth = new BearerTokenAuth('test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('Bearer test', $request->getHeaderLine('Authorization'));
    }
}

