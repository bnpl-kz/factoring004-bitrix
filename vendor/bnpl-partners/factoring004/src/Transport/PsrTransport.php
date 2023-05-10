<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class PsrTransport extends AbstractTransport
{
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;
    private ClientInterface $client;

    /**
     * @param \Psr\Http\Message\RequestFactoryInterface $requestFactory
     * @param \Psr\Http\Message\StreamFactoryInterface $streamFactory
     * @param \Psr\Http\Message\UriFactoryInterface $uriFactory
     * @param \Psr\Http\Client\ClientInterface $client
     */
    public function __construct(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        UriFactoryInterface $uriFactory,
        ClientInterface $client
    ) {
        parent::__construct();

        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
        $this->client = $client;
    }

    protected function createRequest(string $method, UriInterface $uri): RequestInterface
    {
        return $this->requestFactory->createRequest($method, $uri);
    }

    protected function createStream(string $content): StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }

    protected function createUri(string $uri): UriInterface
    {
        return $this->uriFactory->createUri($uri);
    }

    protected function sendRequest(RequestInterface $request): PsrResponseInterface
    {
        try {
            return $this->client->sendRequest($request);
        } catch (NetworkExceptionInterface $e) {
            throw new NetworkException('Could not connect to ' . $request->getUri(), 0, $e);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException('Unable to send request to ' . $request->getUri(), 0, $e);
        }
    }
}
