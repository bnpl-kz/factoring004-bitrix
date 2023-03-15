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
     */
    public function setBaseUri(string $uri): TransportInterface;

    /**
     * Sets global HTTP headers. Content-Type, User-Agent etc.
     *
     * @param array<string, string> $headers
     */
    public function setHeaders(array $headers): TransportInterface;

    /**
     * Sets authentication method.
     */
    public function setAuthentication(AuthenticationInterface $authentication): TransportInterface;

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
    public function get(string $path, array $query = [], array $headers = []): ResponseInterface;

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
    public function post(string $path, array $data = [], array $headers = []): ResponseInterface;

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
    public function request(string $method, string $path, array $data = [], array $headers = []): ResponseInterface;
}
