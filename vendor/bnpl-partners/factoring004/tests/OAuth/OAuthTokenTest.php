<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use PHPUnit\Framework\TestCase;

class OAuthTokenTest extends TestCase
{
    public function testCreateFromArray(): void
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

    public function testGetAccessToken(): void
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('dGVzdA==', $token->getAccessToken());

        $token = new OAuthToken('dG9rZW4=', 'default', 'Bearer', 3600);
        $this->assertEquals('dG9rZW4=', $token->getAccessToken());
    }

    public function testGetScope(): void
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('default', $token->getScope());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Bearer', 3600);
        $this->assertEquals('test', $token->getScope());
    }

    public function testGetTokenType(): void
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals('Bearer', $token->getTokenType());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Basic', 3600);
        $this->assertEquals('Basic', $token->getTokenType());
    }

    public function testGetExpiresIn(): void
    {
        $token = new OAuthToken('dGVzdA==', 'default', 'Bearer', 3600);
        $this->assertEquals(3600, $token->getExpiresIn());

        $token = new OAuthToken('dG9rZW4=', 'test', 'Basic', 300);
        $this->assertEquals(300, $token->getExpiresIn());
    }

    public function testToArray(): void
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

    public function testJsonSerialize(): void
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

