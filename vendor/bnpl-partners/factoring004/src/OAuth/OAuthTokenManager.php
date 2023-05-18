<?php

namespace BnplPartners\Factoring004\OAuth;

use BadMethodCallException;
use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;

class OAuthTokenManager implements OAuthTokenManagerInterface
{
    const ACCESS_PATH = '/sign-in';
    const REFRESH_PATH = '/refresh';

    /**
     * @var \BnplPartners\Factoring004\Transport\TransportInterface
     */
    private $transport;
    /**
     * @var string
     */
    private $baseUri;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;

    /**
     * @param string $baseUri
     * @param string $username
     * @param string $password
     * @param \BnplPartners\Factoring004\Transport\TransportInterface|null $transport
     */
    public function __construct(
        $baseUri,
        $username,
        $password,
        TransportInterface $transport = null
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

        $this->transport = isset($transport) ? $transport : new GuzzleTransport();
        $this->baseUri = $baseUri;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    public function getAccessToken()
    {
        return $this->manageToken(static::ACCESS_PATH, [
            'username' => $this->username,
            'password' => $this->password,
        ]);
    }

    public function refreshToken($refreshToken)
    {
        return $this->manageToken(static::REFRESH_PATH, compact('refreshToken'));
    }

    /**
     * @return void
     */
    public function revokeToken()
    {
        throw new BadMethodCallException('Method ' . __FUNCTION__ . ' is not supported');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     * @return \BnplPartners\Factoring004\OAuth\OAuthToken
     */
    private function manageToken($path, array $data = [])
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

        throw new OAuthException(
            isset($response->getBody()['message']) ? $response->getBody()['message'] : 'Cannot generate an access token'
        );
    }
}
