<?php

namespace BnplPartners\Factoring004\OAuth;

use PHPUnit\Framework\TestCase;

class OAuthTokenTest extends TestCase
{
    public function testCreateFromArray()
    {
        $expected = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $actual = OAuthToken::createFromArray([
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAccess()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $this->assertEquals('dGVzdA==', $token->getAccess());

        $token = new OAuthToken('dG9rZW4=', 300, 'dGVzdDE=', 3600);
        $this->assertEquals('dG9rZW4=', $token->getAccess());
    }

    public function testAccessExpiresAt()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $this->assertEquals(300, $token->getAccessExpiresAt());

        $token = new OAuthToken('dGVzdA==', 600, 'dGVzdDE=', 3600);
        $this->assertEquals(600, $token->getAccessExpiresAt());
    }

    public function testGetRefresh()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $this->assertEquals('dGVzdDE=', $token->getRefresh());

        $token = new OAuthToken('dGVzdA==', 300, 'dG9rZW4=', 3600);
        $this->assertEquals('dG9rZW4=', $token->getRefresh());
    }

    public function testGetRefreshExpiresAt()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $this->assertEquals(3600, $token->getRefreshExpiresAt());

        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 7200);
        $this->assertEquals(7200, $token->getRefreshExpiresAt());
    }

    public function testToArray()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $expected = [
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ];

        $this->assertEquals($expected, $token->toArray());
    }

    public function testJsonSerialize()
    {
        $token = new OAuthToken('dGVzdA==', 300, 'dGVzdDE=', 3600);
        $expected = [
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ];

        $this->assertJsonStringEqualsJsonString(json_encode($expected), json_encode($token));
    }
}

