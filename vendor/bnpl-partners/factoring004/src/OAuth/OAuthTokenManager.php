<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BadMethodCallException;
use BnplPartners\Factoring004\Auth\BasicAuth;
use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;

class OAuthTokenManager implements OAuthTokenManagerInterface
{
    const ACCESS_PATH = '/token';
    const REVOKE_PATH = '/revoke';

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
    private $consumerKey;
    /**
     * @var string
     */
    private $consumerSecret;

    /**
     * @param \BnplPartners\Factoring004\Transport\TransportInterface|null $transport
     */
    public function __construct(
        string $baseUri,
        string $consumerKey,
        string $consumerSecret,
        $transport = null
    ) {
        if (!$baseUri) {
            throw new InvalidArgumentException('Base URI cannot be empty');
        }

        if (!$consumerKey) {
            throw new InvalidArgumentException('Consumer key cannot be empty');
        }

        if (!$consumerSecret) {
            throw new InvalidArgumentException('Consumer secret cannot be empty');
        }

        $this->transport = $transport ?? new GuzzleTransport();
        $this->baseUri = $baseUri;
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
    }

    public function getAccessToken(): OAuthToken
    {
        $this->transport->setBaseUri($this->baseUri);
        $this->transport->setAuthentication(new BasicAuth($this->consumerKey, $this->consumerSecret));

        try {
            $response = $this->transport->post(static::ACCESS_PATH, ['grant_type' => 'client_credentials'], [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ]);
        } catch (TransportException $e) {
            throw new OAuthException('Cannot generate an access token', 0, $e);
        }

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return OAuthToken::createFromArray($response->getBody());
        }

        throw new OAuthException('Cannot generate an access token');
    }

    /**
     * @return void
     */
    public function revokeToken()
    {
        throw new BadMethodCallException('Method ' . __FUNCTION__ . ' is not supported');
    }
}
