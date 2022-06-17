<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

interface OAuthTokenManagerInterface
{
    /**
     * Generates new access token. Each call should return new token always.
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function getAccessToken(): OAuthToken;

    /**
     * Revokes any token.
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function revokeToken();
}