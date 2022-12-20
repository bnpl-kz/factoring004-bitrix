<?php

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
     * @return \BnplPartners\Factoring004\Transport\TransportInterface
     */
    public function setBaseUri($uri);

    /**
     * Sets global HTTP headers. Content-Type, User-Agent etc.
     *
     * @param array<string, string> $headers
     * @return \BnplPartners\Factoring004\Transport\TransportInterface
     */
    public function setHeaders(array $headers);

    /**
     * Sets authentication method.
     * @return \BnplPartners\Factoring004\Transport\TransportInterface
     */
    public function setAuthentication(AuthenticationInterface $authentication);

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
    public function get($path, array $query = [], array $headers = []);

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
    public function post($path, array $data = [], array $headers = []);

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
    public function request($method, $path, array $data = [], array $headers = []);
}
