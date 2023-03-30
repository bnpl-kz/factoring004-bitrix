<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use PHPUnit\Framework\TestCase;

class OAuthTokenRefreshPolicyTest extends TestCase
{
    public function testALWAYSRETRIEVE(): void
    {
        $this->assertEquals(OAuthTokenRefreshPolicy::ALWAYS_RETRIEVE(), OAuthTokenRefreshPolicy::from('always_retrieve'));
    }

    public function testALWAYSREFRESH(): void
    {
        $this->assertEquals(OAuthTokenRefreshPolicy::ALWAYS_REFRESH(), OAuthTokenRefreshPolicy::from('always_refresh'));
    }
}

