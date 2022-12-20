<?php

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\AbstractTestCase;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class CacheOAuthTokenManagerTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function testGetAccessTokenWithCacheMiss()
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
     * @return void
     */
    public function testGetAccessTokenWithCache()
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
     * @return void
     */
    public function testGetAccessTokenWhenCacheGetMethodIsFailed()
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
            ->willThrowException(new Anonymous__cf9f00b301d16ec217fa09f5f6653050__0());

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function testGetAccessTokenWhenCacheSetMethodIsFailed()
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
            ->willThrowException(new Anonymous__cf9f00b301d16ec217fa09f5f6653050__1());

        $cache->expects($this->once())
            ->method('get')
            ->with($cacheKey, $this->anything())
            ->willReturn(null);

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);

        $this->assertSame($token, $cacheManager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function testRevokeToken()
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
     * @return void
     */
    public function testRevokeTokenIsFailed()
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
     * @return void
     */
    public function testRevokeTokenWhenCacheIsFailed()
    {
        $cacheKey = 'key';

        $manager = $this->createMock(OAuthTokenManagerInterface::class);
        $manager->expects($this->once())->method('revokeToken');

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('delete')
            ->with($cacheKey)
            ->willThrowException(new Anonymous__cf9f00b301d16ec217fa09f5f6653050__2());

        $cacheManager = new CacheOAuthTokenManager($manager, $cache, $cacheKey);
        $cacheManager->revokeToken();
    }
}
class Anonymous__cf9f00b301d16ec217fa09f5f6653050__0 extends \InvalidArgumentException implements InvalidArgumentException
{
}
class Anonymous__cf9f00b301d16ec217fa09f5f6653050__1 extends \InvalidArgumentException implements InvalidArgumentException
{
}
class Anonymous__cf9f00b301d16ec217fa09f5f6653050__2 extends \InvalidArgumentException implements InvalidArgumentException
{
}

