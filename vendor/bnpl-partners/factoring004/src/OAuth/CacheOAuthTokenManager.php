<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CacheOAuthTokenManager implements OAuthTokenManagerInterface
{
    private OAuthTokenManagerInterface $tokenManager;
    private CacheInterface $cache;
    private string $cacheKey;
    private OAuthTokenRefreshPolicy $refreshPolicy;

    public function __construct(
        OAuthTokenManagerInterface $tokenManager,
        CacheInterface $cache,
        string $cacheKey,
        OAuthTokenRefreshPolicy $refreshPolicy = null
    ) {
        $this->tokenManager = $tokenManager;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->refreshPolicy = $refreshPolicy ?? OAuthTokenRefreshPolicy::ALWAYS_RETRIEVE();
    }

    /**
     * @psalm-suppress InvalidCatch
     */
    public function getAccessToken(): OAuthToken
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

    public function refreshToken(string $refreshToken): OAuthToken
    {
        $token = $this->tokenManager->refreshToken($refreshToken);

        $this->storeToken($token);

        return $token;
    }

    public function revokeToken(): void
    {
        $this->clearCache();
        $this->tokenManager->revokeToken();
    }

    /**
     * @psalm-suppress InvalidCatch
     */
    public function clearCache(): void
    {
        try {
            $this->cache->delete($this->cacheKey);
        } catch (InvalidArgumentException $e) {
            // do nothing
        }
    }

    /**
     * @psalm-suppress InvalidCatch
     */
    private function storeToken(OAuthToken $token): void
    {
        try {
            $this->cache->set($this->cacheKey, $token->toArray(), $token->getRefreshExpiresAt());
        } catch (InvalidArgumentException $e) {
            // do nothing
        }
    }
}
