<?php

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class NoAuth implements AuthenticationInterface
{
    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function apply(RequestInterface $request)
    {
        return $request;
    }
}
