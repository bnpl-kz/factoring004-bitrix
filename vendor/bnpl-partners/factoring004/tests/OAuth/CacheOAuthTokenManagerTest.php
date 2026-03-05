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
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
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
            ->with($cacheKey, $token->toArray(), $token->getRefreshExpiresAt())
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
            'access' => 'dGVzdA==',
            'accessExpiresAt' => time() + 60,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => time() + 3600,
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
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
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
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->withAnyParameters()
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $token->toArray(), $token->getRefreshExpiresAt())
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
    public function testGetAccessTokenWithAlwaysRefreshPolicy(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => time() - 60,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => time() + 3600,
        ]);

        $newToken = OAuthToken::createFromArray([
            'access' => 'dGVzdDE=',
            'accessExpiresAt' => time() + 60,
            'refresh' => 'dGVzdDI=',
            'refreshExpiresAt' => time() + 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->never())->method('getAccessToken');
        $manager->expects($this->once())
            ->method('refreshToken')
            ->with($token->getRefresh())
            ->willReturn($newToken);

        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn($token->toArray());

        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $newToken->toArray(), $newToken->getRefreshExpiresAt())
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager(
            $manager,
            $cache,
            $cacheKey,
            OAuthTokenRefreshPolicy::ALWAYS_REFRESH(),
        );

        $this->assertSame($newToken, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWithAlwaysRefreshPolicyWhenRefreshTokenExpired(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => time() - 120,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => time() - 60,
        ]);

        $newToken = OAuthToken::createFromArray([
            'access' => 'dGVzdDE=',
            'accessExpiresAt' => time() + 60,
            'refresh' => 'dGVzdDI=',
            'refreshExpiresAt' => time() + 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->never())->method('refreshToken');
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($newToken);

        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn($token->toArray());

        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $newToken->toArray(), $newToken->getRefreshExpiresAt())
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager(
            $manager,
            $cache,
            $cacheKey,
            OAuthTokenRefreshPolicy::ALWAYS_REFRESH(),
        );

        $this->assertSame($newToken, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenWithAlwaysRetrievePolicy(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => time() - 60,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => time() + 3600,
        ]);

        $newToken = OAuthToken::createFromArray([
            'access' => 'dGVzdDE=',
            'accessExpiresAt' => time() + 60,
            'refresh' => 'dGVzdDI=',
            'refreshExpiresAt' => time() + 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->never())->method('refreshToken');
        $manager->expects($this->once())
            ->method('getAccessToken')
            ->willReturn($newToken);

        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn($token->toArray());

        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $newToken->toArray(), $newToken->getRefreshExpiresAt())
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager(
            $manager,
            $cache,
            $cacheKey,
            OAuthTokenRefreshPolicy::ALWAYS_RETRIEVE(),
        );

        $this->assertSame($newToken, $cacheManager->getAccessToken());
    }

    public function testRefreshToken(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('refreshToken')
            ->with($token->getAccess())
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('set')
            ->with($cacheKey, $token->toArray(), $token->getRefreshExpiresAt())
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->refreshToken($token->getAccess()));
    }

    public function testRefreshTokenIsFailed(): void
    {
        $cacheKey = 'key';
        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('refreshToken')
            ->withAnyParameters()
            ->willThrowException(new OAuthException('Test'));

        $cache = $this->createStub(CacheInterface::class);
        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->expectException(OAuthException::class);
        $this->expectExceptionMessage('Test');

        $cacheManager->refreshToken('test');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRefreshTokenWhenCacheIsFailed(): void
    {
        $cacheKey = 'key';
        $token = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ]);

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())
            ->method('refreshToken')
            ->withAnyParameters()
            ->willReturn($token);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('set')
            ->withAnyParameters()
            ->willThrowException(new class() extends \InvalidArgumentException implements InvalidArgumentException {});

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertEquals($token, $cacheManager->refreshToken('test'));
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

    public function testClearCache(): void
    {
        $cacheKey = 'key';
        $manager = $this->createMock(OAuthTokenManagerInterface::class);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with($cacheKey)
            ->willReturn(true);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);
        $cacheManager->clearCache();
    }
}

