<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class OAuthToken implements JsonSerializable, ArrayInterface
{
    private string $access;
    private int $accessExpiresAt;
    private string $refresh;
    private int $refreshExpiresAt;

    public function __construct(string $access, int $accessExpiresAt, string $refresh, int $refreshExpiresAt)
    {
        $this->access = $access;
        $this->accessExpiresAt = $accessExpiresAt;
        $this->refresh = $refresh;
        $this->refreshExpiresAt = $refreshExpiresAt;
    }

    /**
     * @param array<string, mixed> $token
     * @psalm-param array{access: string, accessExpiresAt: int, refresh: string, refreshExpiresAt: int} $token
     */
    public static function createFromArray(array $token): OAuthToken
    {
        return new self($token['access'], $token['accessExpiresAt'], $token['refresh'], $token['refreshExpiresAt']);
    }

    public function getAccess(): string
    {
        return $this->access;
    }

    public function getAccessExpiresAt(): int
    {
        return $this->accessExpiresAt;
    }

    public function getRefresh(): string
    {
        return $this->refresh;
    }

    public function getRefreshExpiresAt(): int
    {
        return $this->refreshExpiresAt;
    }

    /**
     * @psalm-return array{access: string, accessExpiresAt: int, refresh: string, refreshExpiresAt: int}
     */
    public function toArray(): array
    {
        return [
            'access' => $this->getAccess(),
            'accessExpiresAt' => $this->getAccessExpiresAt(),
            'refresh' => $this->getRefresh(),
            'refreshExpiresAt' => $this->getRefreshExpiresAt(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
