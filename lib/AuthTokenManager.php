<?php

namespace Bnpl\Payment;

use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Bitrix\Main\Application;

class AuthTokenManager
{
    protected $manager;

    public function __construct(string $login, string $password, string $apiHost, TransportInterface $transport, Application $instance)
    {
        $cache = new BitrixSimpleCache($instance->getCache());
        $tokenManager = new OAuthTokenManager($apiHost . '/users/api/v1', $login, $password, $transport);
        $this->manager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment');
    }

    public static function init(string $login, string $password, string $apiHost, TransportInterface $transport, Application $instance): AuthTokenManager
    {
        return new self($login, $password, $apiHost, $transport, $instance);
    }

    public function getToken(): string
    {
        return $this->manager->getAccessToken()->getAccess();
    }

    public function clearCache(): void
    {
        $this->manager->clearCache();
    }
}