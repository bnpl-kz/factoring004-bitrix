<?php

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\AbstractEnum;

/**
 * @method static static ALWAYS_RETRIEVE()
 * @method static static ALWAYS_REFRESH()
 *
 * @psalm-immutable
 */
final class OAuthTokenRefreshPolicy extends AbstractEnum
{
    const ALWAYS_RETRIEVE = 'always_retrieve';
    const ALWAYS_REFRESH = 'always_refresh';
}
