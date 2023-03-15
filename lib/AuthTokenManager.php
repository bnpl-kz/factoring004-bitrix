<?php

namespace Bnpl\Payment;

use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;

class AuthTokenManager
{
    protected $manager;
    public function __construct(string $login, string $password, string $apiHost, $transport, $instance)
    {
        $cache = new BitrixSimpleCache($instance->getCache());
        $tokenManager = new OAuthTokenManager($apiHost . '/users/api/v1', $login, $password, $transport);
        $this->manager = new CacheOAuthTokenManager($tokenManager, $cache, 'bnpl.payment');
    }

    public static function init(string $login, string $password, string $apiHost, $transport, $instance): AuthTokenManager
    {
        return new self($login, $password, $apiHost, $transport, $instance);
    }

    public function getToken()
    {
        return $this->manager->getAccessToken()->getAccess();
    }
}