<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class NoAuth implements AuthenticationInterface
{
    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function apply($request): RequestInterface
    {
        return $request;
    }
}
