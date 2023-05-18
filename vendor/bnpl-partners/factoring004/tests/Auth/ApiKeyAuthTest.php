<?php

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use BnplPartners\Factoring004\AbstractTestCase;

class ApiKeyAuthTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testApply()
    {
        $auth = new ApiKeyAuth('test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('test', $request->getHeaderLine('apiKey'));
    }
}

