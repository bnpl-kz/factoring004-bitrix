<?php

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\AbstractTestCase;

class OAuthTokenTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $actual = OAuthToken::createFromArray([
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetAccessToken()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('dGVzdA==', $token->getAccessToken());

        $token = new OAuthToken('dG9rZW4=', 'default', 'Bearer', 3600);
        $this->assertEquals('dG9rZW4=', $token->getAccessToken());
    }

    /**
     * @return void
     */
    public function testGetScope()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('default', $token->getScope());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Bearer', 3600);
        $this->assertEquals('test', $token->getScope());
    }

    /**
     * @return void
     */
    public function testGetTokenType()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('Bearer', $token->getTokenType());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Basic', 3600);
        $this->assertEquals('Basic', $token->getTokenType());
    }

    /**
     * @return void
     */
    public function testGetExpiresIn()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals(3600, $token->getExpiresIn());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Basic', 300);
        $this->assertEquals(300, $token->getExpiresIn());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $expected = [
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ];

        $this->assertEquals($expected, $token->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $expected = [
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($token));
    }
}

