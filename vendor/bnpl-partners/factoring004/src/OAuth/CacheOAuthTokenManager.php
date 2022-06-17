<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CacheOAuthTokenManager implements OAuthTokenManagerInterface
{
    /**
     * @var \BnplPartners\Factoring004\OAuth\OAuthTokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $cacheKey;

    public function __construct(OAuthTokenManagerInterface $tokenManager, CacheInterface $cache, string $cacheKey)
    {
        $this->tokenManager = $tokenManager;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    public function getAccessToken(): OAuthToken
    {
        return $this->retrieveAccessToken();
    }

    /**
     * @return void
     */
    public function revokeToken()
    {
        $this->clearCache();
        $this->tokenManager->revokeToken();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @psalm-suppress InvalidCatch
     */
    private function retrieveAccessToken(): OAuthToken
    {
        try {
            $tokenData = $this->cache->get($this->cacheKey);
        } catch (InvalidArgumentException $e) {
            return $this->tokenManager->getAccessToken();
        }

        if ($tokenData) {
            return OAuthToken::createFromArray($tokenData);
        }

        $token = $this->tokenManager->getAccessToken();

        try {
            $this->cache->set($this->cacheKey, $token->toArray(), $token->getExpiresIn());
        } catch (InvalidArgumentException $e) {
            // do nothing
        }

        return $token;
    }

    /**
     * @psalm-suppress InvalidCatch
     * @return void
     */
    private function clearCache()
    {
        try {
            $this->cache->delete($this->cacheKey);
        } catch (InvalidArgumentException $e) {
            // do nothing
        }
    }
}
