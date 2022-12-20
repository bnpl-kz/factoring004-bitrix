<?php

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use BnplPartners\Factoring004\AbstractTestCase;

class BasicAuthTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testApply()
    {
        $auth = new BasicAuth('test', 'test');
        $request = new Request('GET', '/');

        $request = $auth->apply($request);

        $this->assertEquals('Basic ' . base64_encode('test:test'), $request->getHeaderLine('Authorization'));
    }
}

