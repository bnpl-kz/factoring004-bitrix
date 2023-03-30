<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use MyCLabs\Enum\Enum;

/**
 * @method static static ALWAYS_RETRIEVE()
 * @method static static ALWAYS_REFRESH()
 *
 * @psalm-immutable
 */
final class OAuthTokenRefreshPolicy extends Enum
{
    private const ALWAYS_RETRIEVE = 'always_retrieve';
    private const ALWAYS_REFRESH = 'always_refresh';
}
