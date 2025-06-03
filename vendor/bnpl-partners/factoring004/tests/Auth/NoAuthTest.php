<?php

namespace BnplPartners\Factoring004\Auth;

use GuzzleHttp\Psr7\Request;
use BnplPartners\Factoring004\AbstractTestCase;

class NoAuthTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testApply()
    {
        $auth = new NoAuth();
        $expected = new Request('GET', '/');

        $request = $auth->apply($expected);

        $this->assertEquals($expected, $request);
    }
}

