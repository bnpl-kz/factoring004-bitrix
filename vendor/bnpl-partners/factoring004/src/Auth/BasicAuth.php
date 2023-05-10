<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class BasicAuth implements AuthenticationInterface
{
    private const HEADER_NAME = 'Authorization';
    private const AUTH_SCHEMA = 'Basic';

    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function apply(RequestInterface $request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, static::AUTH_SCHEMA . ' ' . $this->encodeCredentials());
    }

    private function encodeCredentials(): string
    {
        return base64_encode($this->username . ':' . $this->password);
    }
}
