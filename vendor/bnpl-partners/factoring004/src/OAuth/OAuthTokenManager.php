<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BadMethodCallException;
use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;

class OAuthTokenManager implements OAuthTokenManagerInterface
{
    public const ACCESS_PATH = '/sign-in';
    public const REFRESH_PATH = '/refresh';

    private TransportInterface $transport;
    private string $baseUri;
    private string $username;
    private string $password;

    public function __construct(
        string $baseUri,
        string $username,
        string $password,
        ?TransportInterface $transport = null
    ) {
        if (!$baseUri) {
            throw new InvalidArgumentException('Base URI cannot be empty');
        }

        if (!$username) {
            throw new InvalidArgumentException('Username cannot be empty');
        }

        if (!$password) {
            throw new InvalidArgumentException('Password cannot be empty');
        }

        $this->transport = $transport ?? new GuzzleTransport();
        $this->baseUri = $baseUri;
        $this->username = $username;
        $this->password = $password;
    }

    public function getAccessToken(): OAuthToken
    {
        return $this->manageToken(static::ACCESS_PATH, [
            'username' => $this->username,
            'password' => $this->password,
        ]);
    }

    public function refreshToken(string $refreshToken): OAuthToken
    {
        return $this->manageToken(static::REFRESH_PATH, compact('refreshToken'));
    }

    public function revokeToken(): void
    {
        throw new BadMethodCallException('Method ' . __FUNCTION__ . ' is not supported');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    private function manageToken(string $path, array $data = []): OAuthToken
    {
        $this->transport->setBaseUri($this->baseUri);

        try {
            $response = $this->transport->post($path, $data, ['Content-Type' => 'application/json']);
        } catch (TransportException $e) {
            throw new OAuthException('Cannot generate an access token', 0, $e);
        }

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return OAuthToken::createFromArray($response->getBody());
        }

        throw new OAuthException($response->getBody()['message'] ?? 'Cannot generate an access token');
    }
}
