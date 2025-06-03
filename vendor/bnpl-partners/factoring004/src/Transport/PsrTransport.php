<?php

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
    /**
     * @var \Psr\Http\Message\RequestFactoryInterface
     */
    private $requestFactory;
    /**
     * @var \Psr\Http\Message\StreamFactoryInterface
     */
    private $streamFactory;
    /**
     * @var \Psr\Http\Message\UriFactoryInterface
     */
    private $uriFactory;
    /**
     * @var \Psr\Http\Client\ClientInterface
     */
    private $client;

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

    /**
     * @param string $method
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequest($method, UriInterface $uri)
    {
        return $this->requestFactory->createRequest($method, $uri);
    }

    /**
     * @param string $content
     * @return \Psr\Http\Message\StreamInterface
     */
    protected function createStream($content)
    {
        return $this->streamFactory->createStream($content);
    }

    /**
     * @param string $uri
     * @return \Psr\Http\Message\UriInterface
     */
    protected function createUri($uri)
    {
        return $this->uriFactory->createUri($uri);
    }

    /**
     * @return PsrResponseInterface
     */
    protected function sendRequest(RequestInterface $request)
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
