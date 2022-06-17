<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\Auth\NoAuth;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

abstract class AbstractTransport implements TransportInterface
{
    use LoggerAwareTrait;

    const METHODS_WITHOUT_BODY = ['GET', 'HEAD', 'OPTIONS', 'DELETE'];
    const DEFAULT_CONTENT_TYPE = 'application/json';
    const LOGGER_PREFIX = 'bnpl-partners/factoring004';

    /**
     * @var array<string, string>
     */
    protected $headers = [];
    /**
     * @var \Psr\Http\Message\UriInterface|null
     */
    protected $baseUri;
    /**
     * @var \BnplPartners\Factoring004\Auth\AuthenticationInterface
     */
    protected $authentication;

    public function __construct()
    {
        $this->authentication = new NoAuth();
        $this->setLogger(new NullLogger());
    }

    /**
     * @param string $uri
     */
    public function setBaseUri($uri): TransportInterface
    {
        $this->baseUri = $this->createUri($uri);
        return $this;
    }

    /**
     * @param mixed[] $headers
     */
    public function setHeaders($headers): TransportInterface
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @param \BnplPartners\Factoring004\Auth\AuthenticationInterface $authentication
     */
    public function setAuthentication($authentication): TransportInterface
    {
        $this->authentication = $authentication;
        return $this;
    }

    /**
     * @param string $path
     * @param mixed[] $query
     * @param mixed[] $headers
     */
    public function get($path, $query = [], $headers = []): ResponseInterface
    {
        return $this->request(__FUNCTION__, $path, $query, $headers);
    }

    /**
     * @param string $path
     * @param mixed[] $data
     * @param mixed[] $headers
     */
    public function post($path, $data = [], $headers = []): ResponseInterface
    {
        return $this->request(__FUNCTION__, $path, $data, $headers);
    }

    /**
     * @param string $method
     * @param string $path
     * @param mixed[] $data
     * @param mixed[] $headers
     */
    public function request($method, $path, $data = [], $headers = []): ResponseInterface
    {
        $request = $this->prepareRequest(strtoupper($method), $path, $data, $headers);

        /** @psalm-suppress PossiblyNullReference */
        $this->logger->debug(
            static::LOGGER_PREFIX . ': Request: ' .
            sprintf(
                '%s %s %s',
                $request->getMethod(),
                (string) $request->getUri(),
                (string) $request->getBody()
            )
        );

        $response = $this->sendRequest($request);

        $this->logger->debug(
            static::LOGGER_PREFIX . ': Response: '.
            sprintf(
                '%d %s %s',
                $response->getStatusCode(),
                (string) $request->getUri(),
                (string) $response->getBody()
            )
        );

        return $this->convertResponse($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @param string $method
     * @param string $path
     * @param mixed[] $data
     * @param mixed[] $headers
     */
    protected function prepareRequest($method, $path, $data, $headers): RequestInterface
    {
        $isWithoutBody = in_array($method, static::METHODS_WITHOUT_BODY, true);
        $query = $isWithoutBody ? $data : [];

        $request = $this->createRequest($method, $this->prepareUri($path, $query));
        $request = $this->mergeRequestHeaders($request, $headers);

        if (!$isWithoutBody) {
            $stream = $this->createStream($this->serializeData($data, $request));
            $request = $request->withBody($stream);
        }

        return $this->authentication->apply($request);
    }

    /**
     * @param array<string, mixed> $query
     * @param string $path
     */
    protected function prepareUri($path, $query): UriInterface
    {
        $uri = $this->baseUri ?? $this->createUri('/');

        $path = rtrim($uri->getPath(), '/') . '/' . ltrim($path, '/');
        $uri = $uri->withPath($path);

        return $query ? $uri->withQuery(http_build_query($query)) : $uri;
    }

    /**
     * @param string $method
     * @param \Psr\Http\Message\UriInterface $uri
     */
    abstract protected function createRequest($method, $uri): RequestInterface;

    /**
     * @param string $content
     */
    abstract protected function createStream($content): StreamInterface;

    /**
     * @param string $uri
     */
    abstract protected function createUri($uri): UriInterface;

    /**
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @param \Psr\Http\Message\RequestInterface $request
     */
    abstract protected function sendRequest($request): PsrResponseInterface;

    /**
     * @param array<string, string> $headers
     * @param \Psr\Http\Message\RequestInterface $request
     */
    protected function mergeRequestHeaders($request, $headers): RequestInterface
    {
        $headers = array_merge($this->headers, $headers);

        foreach ($headers as $name => $header) {
            $request = $request->withHeader($name, $header);
        }

        return $request;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @param \Psr\Http\Message\RequestInterface $request
     */
    protected function serializeData($data, $request): string
    {
        if (!$data) {
            return '';
        }

        $contentType = $request->getHeaderLine('Content-Type') ?: static::DEFAULT_CONTENT_TYPE;

        if (strpos($contentType, 'json') !== false) {
            $json = json_encode($data);

            if (json_last_error() === JSON_ERROR_NONE) {
                return $json;
            }

            throw new DataSerializationException('Invalid JSON format');
        }

        if ($contentType === 'application/x-www-form-urlencoded') {
            return http_build_query($data);
        }

        throw new DataSerializationException('Unsupported content type ' . $contentType);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @param PsrResponseInterface $response
     */
    protected function convertResponse($response): ResponseInterface
    {
        return Response::createFromPsrResponse($response);
    }
}
