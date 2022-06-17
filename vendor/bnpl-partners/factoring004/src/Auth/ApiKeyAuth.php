<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class ApiKeyAuth implements AuthenticationInterface
{
    const HEADER_NAME = 'apikey';

    /**
     * @var string
     */
    private $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function apply($request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, $this->apiKey);
    }
}
