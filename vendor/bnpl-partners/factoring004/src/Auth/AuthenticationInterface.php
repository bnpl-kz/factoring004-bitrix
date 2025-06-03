<?php

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

interface AuthenticationInterface
{
    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function apply(RequestInterface $request);
}
