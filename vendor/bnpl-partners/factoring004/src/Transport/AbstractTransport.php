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

    protected const METHODS_WITHOUT_BODY = ['GET', 'HEAD', 'OPTIONS', 'DELETE'];
    protected const DEFAULT_CONTENT_TYPE = 'application/json';
    public const LOGGER_PREFIX = 'bnpl-partners/factoring004';

    /**
     * @var array<string, string>
     */
    protected array $headers = [];
    protected ?UriInterface $baseUri = null;
    protected AuthenticationInterface $authentication;

    public function __construct()
    {
        $this->authentication = new NoAuth();
        $this->setLogger(new NullLogger());
    }

    public function setBaseUri(string $uri): TransportInterface
    {
        $this->baseUri = $this->createUri($uri);
        return $this;
    }

    public function setHeaders(array $headers): TransportInterface
    {
        $this->headers = $headers;
        return $this;
    }

    public function setAuthentication(AuthenticationInterface $authentication): TransportInterface
    {
        $this->authentication = $authentication;
        return $this;
    }

    public function get(string $path, array $query = [], array $headers = []): ResponseInterface
    {
        return $this->request(__FUNCTION__, $path, $query, $headers);
    }

    public function post(string $path, array $data = [], array $headers = []): ResponseInterface
    {
        return $this->request(__FUNCTION__, $path, $data, $headers);
    }

    public function request(string $method, string $path, array $data = [], array $headers = []): ResponseInterface
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
     */
    protected function prepareRequest(string $method, string $path, array $data, array $headers): RequestInterface
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
     */
    protected function prepareUri(string $path, array $query): UriInterface
    {
        $uri = $this->baseUri ?? $this->createUri('/');

        $path = rtrim($uri->getPath(), '/') . '/' . ltrim($path, '/');
        $uri = $uri->withPath($path);

        return $query ? $uri->withQuery(http_build_query($query)) : $uri;
    }

    abstract protected function createRequest(string $method, UriInterface $uri): RequestInterface;

    abstract protected function createStream(string $content): StreamInterface;

    abstract protected function createUri(string $uri): UriInterface;

    /**
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    abstract protected function sendRequest(RequestInterface $request): PsrResponseInterface;

    /**
     * @param array<string, string> $headers
     */
    protected function mergeRequestHeaders(RequestInterface $request, array $headers): RequestInterface
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
     */
    protected function serializeData(array $data, RequestInterface $request): string
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
     */
    protected function convertResponse(PsrResponseInterface $response): ResponseInterface
    {
        return Response::createFromPsrResponse($response);
    }
}
