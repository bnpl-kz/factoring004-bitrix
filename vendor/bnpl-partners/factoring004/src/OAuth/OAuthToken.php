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

    /**
     * @param string $accessToken
     * @param string $scope
     * @param string $tokenType
     * @param int $expiresIn
     */
    public function __construct($accessToken, $scope, $tokenType, $expiresIn)
    {
        $this->accessToken = $accessToken;
        $this->scope = $scope;
        $this->expiresIn = $expiresIn;
        $this->tokenType = $tokenType;
    }

    /**
     * @param array<string, mixed> $token
     * @psalm-param array{access_token: string, scope: string, expires_in: int, token_type: string} $token
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public static function createFromArray(array $token)
    {
        return new self($token['access_token'], $token['scope'], $token['token_type'], $token['expires_in']);
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return int In seconds.
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @psalm-return array{access_token: string, scope: string, expires_in: int, token_type: string}
     * @return mixed[]
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
