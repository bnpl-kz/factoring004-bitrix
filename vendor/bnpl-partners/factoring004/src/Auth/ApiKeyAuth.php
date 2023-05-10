<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Auth;

use Psr\Http\Message\RequestInterface;

class ApiKeyAuth implements AuthenticationInterface
{
    private const HEADER_NAME = 'apikey';

    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function apply(RequestInterface $request): RequestInterface
    {
        return $request->withHeader(static::HEADER_NAME, $this->apiKey);
    }
}
