<?php

namespace BnplPartners\Factoring004\OAuth;

interface OAuthTokenManagerInterface
{
    /**
     * Generates new access token. Each call should return new token always.
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public function getAccessToken();

    /**
     * Revokes any token.
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function revokeToken();
}
