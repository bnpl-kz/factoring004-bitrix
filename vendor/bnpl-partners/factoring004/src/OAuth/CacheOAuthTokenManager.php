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
     * @var \BnplPartners\Factoring004\OAuth\OAuthTokenRefreshPolicy 
     */
    private $refreshPolicy;

    /**
     * @param string $cacheKey
     */
    public function __construct(
        OAuthTokenManagerInterface $tokenManager,
        CacheInterface $cache,
        $cacheKey,
        OAuthTokenRefreshPolicy $refreshPolicy = null
    ) {
        $this->tokenManager = $tokenManager;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->refreshPolicy = $refreshPolicy ?: OAuthTokenRefreshPolicy::ALWAYS_RETRIEVE();
    }

    /**
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     * @psalm-suppress InvalidCatch
     */
    public function getAccessToken()
    {
        try {
            $tokenData = $this->cache->get($this->cacheKey);
        } catch (InvalidArgumentException $e) {
            return $this->tokenManager->getAccessToken();
        }

        if (!$tokenData) {
            $token = $this->tokenManager->getAccessToken();
            $this->storeToken($token);

            return $token;
        }

        $token = OAuthToken::createFromArray($tokenData);

        if ($token->getAccessExpiresAt() > time()) {
            return $token;
        }

        if ($token->getRefreshExpiresAt() > time() && $this->refreshPolicy->equals(
                OAuthTokenRefreshPolicy::ALWAYS_REFRESH()
            )) {
            return $this->refreshToken($token->getRefresh());
        }

        $token = $this->tokenManager->getAccessToken();

        $this->storeToken($token);

        return $token;
    }

    /**
     * @param string $refreshToken
     *
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function refreshToken($refreshToken)
    {
        $token = $this->tokenManager->refreshToken($refreshToken);

        $this->storeToken($token);

        return $token;
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
     * @psalm-suppress InvalidCatch
     * @return void
     */
    public function clearCache()
    {
        try {
            $this->cache->delete($this->cacheKey);
        } catch (InvalidArgumentException $e) {
            // do nothing
        }
    }

    /**
     * @psalm-suppress InvalidCatch
     *
     * @return void
     */
    private function storeToken(OAuthToken $token)
    {
        try {
            $this->cache->set($this->cacheKey, $token->toArray(), $token->getRefreshExpiresAt());
        } catch (InvalidArgumentException $e) {
            // do nothing
        }
    }
}
