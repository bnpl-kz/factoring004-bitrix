<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class GuzzleTransport extends AbstractTransport
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @param \GuzzleHttp\ClientInterface|null $client
     */
    public function __construct($client = null)
    {
        parent::__construct();

        $this->client = $client ?? new Client();
    }

    /**
     * @param string $method
     * @param \Psr\Http\Message\UriInterface $uri
     */
    protected function createRequest($method, $uri): RequestInterface
    {
        return new Request($method, $uri);
    }

    /**
     * @param string $content
     */
    protected function createStream($content): StreamInterface
    {
        return Utils::streamFor($content);
    }

    /**
     * @param string $uri
     */
    protected function createUri($uri): UriInterface
    {
        return new Uri($uri);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    protected function sendRequest($request): PsrResponseInterface
    {
        try {
            return $this->client->send($request);
        } catch (ConnectException $e) {
            throw new NetworkException('Could not send request to ' . $request->getUri(), 0, $e);
        } catch (RequestException $e) {
            $response = $e->getResponse();

            if ($response) {
                return $response;
            }

            throw new TransportException('Request to ' . $request->getUri() . ' is failed', 0, $e);
        } catch (GuzzleException $e) {
            throw new TransportException('Request to ' . $request->getUri() . ' is failed', 0, $e);
        }
    }
}
