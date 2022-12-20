<?php

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use BnplPartners\Factoring004\AbstractTestCase;

class BearerTokenAuthTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testApply()
    {
        $auth = new BearerTokenAuth('test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('Bearer test', $request->getHeaderLine('Authorization'));
    }
}

