<?php

namespace BnplPartners\Factoring004\OAuth;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class OAuthToken implements JsonSerializable, ArrayInterface
{
    /**
     * @var string
     */
    private $access;

    /**
     * @var int
     */
    private $accessExpiresAt;

    /**
     * @var string
     */
    private $refresh;

    /**
     * @var int
     */
    private $refreshExpiresAt;

    /**
     * @param string $access
     * @param int $accessExpiresAt
     * @param string $refresh
     * @param int $refreshExpiresAt
     */
    public function __construct($access, $accessExpiresAt, $refresh, $refreshExpiresAt)
    {
        $this->access = $access;
        $this->accessExpiresAt = $accessExpiresAt;
        $this->refresh = $refresh;
        $this->refreshExpiresAt = $refreshExpiresAt;
    }

    /**
     * @param array<string, mixed> $token
     * @psalm-param array{access: string, accessExpiresAt: int, refresh: string, refreshExpiresAt: int} $token
     *
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public static function createFromArray(array $token)
    {
        return new self($token['access'], $token['accessExpiresAt'], $token['refresh'], $token['refreshExpiresAt']);
    }

    /**
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * @return int
     */
    public function getAccessExpiresAt()
    {
        return $this->accessExpiresAt;
    }

    /**
     * @return string
     */
    public function getRefresh()
    {
        return $this->refresh;
    }

    /**
     * @return int
     */
    public function getRefreshExpiresAt()
    {
        return $this->refreshExpiresAt;
    }

    /**
     * @psalm-return array{access: string, accessExpiresAt: int, refresh: string, refreshExpiresAt: int}
     * @return array<string, mixed>
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
