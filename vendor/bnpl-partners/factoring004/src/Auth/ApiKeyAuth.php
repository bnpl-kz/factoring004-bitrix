<?php

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class ApiKeyAuth implements AuthenticationInterface
{
    const HEADER_NAME = 'apikey';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @param string $apiKey
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return \Psr\Http\Message\RequestInterface
     */
    public function apply(RequestInterface $request)
    {
        return $request->withHeader(static::HEADER_NAME, $this->apiKey);
    }
}
