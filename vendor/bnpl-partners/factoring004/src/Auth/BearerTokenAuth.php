<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class BearerTokenAuth implements AuthenticationInterface
{
    private const HEADER_NAME = 'Authorization';
    private const AUTH_SCHEMA = 'Bearer';

    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function apply(RequestInterface $request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, static::AUTH_SCHEMA . ' ' . $this->token);
    }
}
