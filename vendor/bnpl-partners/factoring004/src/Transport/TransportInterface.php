<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use Psr\Log\LoggerAwareInterface;

/**
 * Abstraction layer over PSR-17 and PSR-18.
 */
interface TransportInterface extends LoggerAwareInterface
{
    /**
     * Base URI of endpoints.
     * @param string $uri
     */
    public function setBaseUri($uri): TransportInterface;

    /**
     * Sets global HTTP headers. Content-Type, User-Agent etc.
     *
     * @param array<string, string> $headers
     */
    public function setHeaders($headers): TransportInterface;

    /**
     * Sets authentication method.
     * @param \BnplPartners\Factoring004\Auth\AuthenticationInterface $authentication
     */
    public function setAuthentication($authentication): TransportInterface;

    /**
     * Sends HTTP GET request to the endpoint.
     *
     * @param string $path Path to the API endpoint. A full URL can be accepted.
     * @param array<string, mixed> $query A map of query parameters.
     * @param array<string, string> $headers A map of HTTP headers. These headers can override global headers.
     *
     * @return \BnplPartners\Factoring004\Transport\ResponseInterface
     *
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function get($path, $query = [], $headers = []): ResponseInterface;

    /**
     * Sends HTTP POST request to the endpoint.
     *
     * @param string $path Path to the API endpoint. A full URL can be accepted.
     * @param array<string, mixed> $data A map of request body parameters.
     * @param array<string, string> $headers A map of HTTP headers. These headers can override global headers.
     *
     * @return \BnplPartners\Factoring004\Transport\ResponseInterface
     *
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function post($path, $data = [], $headers = []): ResponseInterface;

    /**
     * Sends HTTP request using method and parameters.
     *
     * @param string $method An HTTP method to request.
     * @param string $path Path to the API endpoint. A full URL can be accepted.
     * @param array<string, mixed> $data Query parameters for GET and other methods that do not have request body. Request body parameters for otherwise.
     * @param array<string, string> $headers A map of HTTP headers. These headers can override global headers.
     *
     * @return \BnplPartners\Factoring004\Transport\ResponseInterface
     *
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function request($method, $path, $data = [], $headers = []): ResponseInterface;
}
