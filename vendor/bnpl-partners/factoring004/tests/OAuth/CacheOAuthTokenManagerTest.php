<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\Exception\OAuthException;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CacheOAuthTokenManagerTest extends TestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWithCacheMiss(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn(null);

        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $token->toArray(), $token->getExpiresIn())
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWithCache(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->never())->method('getAccessToken');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->never())->method('set');

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn($token->toArray());

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertNotSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWhenCacheGetMethodIsFailed(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->withAnyParameters()
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->never())->method('set');

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willThrowException(new class() extends \InvalidArgumentException implements InvalidArgumentException {});

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWhenCacheSetMethodIsFailed(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->withAnyParameters()
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $token->toArray(), $token->getExpiresIn())
            ->willThrowException(new class() extends \InvalidArgumentException implements InvalidArgumentException {});

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn(null);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRevokeToken(): void
    {
        $cacheKey = 'key';

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())->method('revokeToken');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with($cacheKey)
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);
        $cacheManager->revokeToken();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRevokeTokenIsFailed(): void
    {
        $cacheKey = 'key';

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('revokeToken')
            ->willThrowException(new OAuthException('Test'));

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with($cacheKey)
            ->willReturn(false);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('Test');

        $cacheManager->revokeToken();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRevokeTokenWhenCacheIsFailed(): void
    {
        $cacheKey = 'key';

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())->method('revokeToken');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with($cacheKey)
            ->willThrowException(new class() extends \InvalidArgumentException implements InvalidArgumentException {});

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);
        $cacheManager->revokeToken();
    }
}

