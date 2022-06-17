<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class ApiKeyAuthTest extends TestCase
{
    public function testApply(): void
    {
        $auth = new ApiKeyAuth('test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('test', $request->getHeaderLine('apiKey'));
    }
}

