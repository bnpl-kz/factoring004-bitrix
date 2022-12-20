<?php

namespace BnplPartners\Factoring004;

use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\Auth\NoAuth;
use BnplPartners\Factoring004\Transport\ResponseInterface;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;

abstract class AbstractResource
{
    const AUTH_ERROR_CODES = [900901, 900902, 900910];
    const DEFAULT_HEADERS = [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ];

    /**
     * @var \BnplPartners\Factoring004\Transport\TransportInterface
     */
    protected $transport;
    /**
     * @var string
     */
    protected $baseUri;
    /**
     * @var \BnplPartners\Factoring004\Auth\AuthenticationInterface
     */
    protected $authentication;

    /**
     * @param string $baseUri
     * @param \BnplPartners\Factoring004\Auth\AuthenticationInterface|null $authentication
     */
    public function __construct(
        TransportInterface $transport,
        $baseUri,
        AuthenticationInterface $authentication = null
    ) {
        if (!filter_var($baseUri, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Base URI cannot be empty');
        }

        $this->transport = $transport;
        $this->baseUri = $baseUri;
        $this->authentication = isset($authentication) ? $authentication : new NoAuth();
    }

    /**
     * @param string $path
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return \BnplPartners\Factoring004\Transport\ResponseInterface
     *
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    protected function postRequest($path, array $data = [], array $headers = [])
    {
        return $this->request('POST', $path, $data, $headers);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array<string, mixed> $data
     * @param array<string, string> $headers
     *
     * @return \BnplPartners\Factoring004\Transport\ResponseInterface
     *
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    protected function request($method, $path, array $data = [], array $headers = [])
    {
        $this->transport->setBaseUri($this->baseUri);
        $this->transport->setAuthentication($this->authentication);
        $this->transport->setHeaders(static::DEFAULT_HEADERS);

        return $this->transport->request($method, $path, $data, $headers);
    }
}
