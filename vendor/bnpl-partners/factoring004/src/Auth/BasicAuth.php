<?php

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

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function apply(RequestInterface $request)
    {
        return $request->withHeader(static::HEADER_NAME, static::AUTH_SCHEMA . ' ' . $this->encodeCredentials());
    }

    /**
     * @return string
     */
    private function encodeCredentials()
    {
        return base64_encode($this->username . ':' . $this->password);
    }
}
