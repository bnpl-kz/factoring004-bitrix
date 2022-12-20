<?php

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

    /**
     * @param string $cacheKey
     */
    public function __construct(OAuthTokenManagerInterface $tokenManager, CacheInterface $cache, $cacheKey)
    {
        $this->tokenManager = $tokenManager;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    /**
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public function getAccessToken()
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
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    private function retrieveAccessToken()
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
