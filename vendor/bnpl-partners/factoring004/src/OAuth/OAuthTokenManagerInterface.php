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
     * Refreshes early retrieved access token. Each call should refresh the token always.
     *
     * @param string $refreshToken
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public function refreshToken($refreshToken);

    /**
     * Revokes any token.
     *
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return void
     */
    public function revokeToken();
}
