<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class BasicAuth implements AuthenticationInterface
{
    const HEADER_NAME = 'Authorization';
    const AUTH_SCHEMA = 'Basic';

    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function apply($request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, static::AUTH_SCHEMA . ' ' . $this->encodeCredentials());
    }

    private function encodeCredentials(): string
    {
        return base64_encode($this->username . ':' . $this->password);
    }
}
