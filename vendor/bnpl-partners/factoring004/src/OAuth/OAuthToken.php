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
    /**
     * @var string
     */
    private $accessToken;
    /**
     * @var string
     */
    private $scope;
    /**
     * @var int
     */
    private $expiresIn;
    /**
     * @var string
     */
    private $tokenType;

    public function __construct(string $accessToken, string $scope, string $tokenType, int $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->scope = $scope;
        $this->expiresIn = $expiresIn;
        $this->tokenType = $tokenType;
    }

    /**
     * @param array<string, mixed> $token
     * @psalm-param array{access_token: string, scope: string, expires_in: int, token_type: string} $token
     */
    public static function createFromArray($token): OAuthToken
    {
        return new self($token['access_token'], $token['scope'], $token['token_type'], $token['expires_in']);
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return int In seconds.
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * @psalm-return array{access_token: string, scope: string, expires_in: int, token_type: string}
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->getAccessToken(),
            'scope' => $this->getScope(),
            'expires_in' => $this->getExpiresIn(),
            'token_type' => $this->getTokenType(),
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
