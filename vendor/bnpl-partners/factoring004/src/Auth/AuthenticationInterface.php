<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

interface AuthenticationInterface
{
    public function apply(RequestInterface $request): RequestInterface;
}
