<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class BearerTokenAuth implements AuthenticationInterface
{
    const HEADER_NAME = 'Authorization';
    const AUTH_SCHEMA = 'Bearer';

    /**
     * @var string
     */
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function apply($request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, static::AUTH_SCHEMA . ' ' . $this->token);
    }
}
