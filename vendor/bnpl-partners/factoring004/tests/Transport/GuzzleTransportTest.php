<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Auth\ApiKeyAuth;
use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\Auth\BasicAuth;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response as PsrResponse;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

class GuzzleTransportTest extends TestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testBaseUriIsEmpty(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => (string) $request->getUri() === '/'))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testSetBaseUri(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => (string) $request->getUri() === 'http://example.com/'))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setBaseUri('http://example.com');
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testSetBaseUriWithPath(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => (string) $request->getUri() === 'http://example.com/1.0/preapp'))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setBaseUri('http://example.com/1.0');
        $transport->get('/preapp');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testHeadersIsEmpty(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => empty($request->getHeaders())))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testSetHeaders(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Test',
        ];

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => $request->getHeaders() === array_map(fn($item) => [$item], $headers)))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setHeaders($headers);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testOverrideHeaders(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => $request->getHeaders() === [
                    'Content-Type' => ['application/x-www-form-urlencoded'],
                    'Accept' => ['application/json'],
                ])
            )
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        $transport->get('/', [], ['Content-Type' => 'application/x-www-form-urlencoded']);
    }

    /**
     * @dataProvider authenticationsProvider
     */
    public function testSetAuthentication(
        AuthenticationInterface $authentication,
        string $expectedHeaderName,
        string $expectedHeaderValue
    ): void {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with(
                $this->callback(fn($request) => $request->getHeaderLine($expectedHeaderName) === $expectedHeaderValue)
            )
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setAuthentication($authentication);

        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testGet(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => $request->getUri()->getPath() === '/test'))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);

        $this->assertEquals(new Response(200, [], ['status' => true, 'message' => 'text']), $transport->get('/test'));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testGetWithQuery(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $query = [
            'a' => 15,
            'b' => 40,
            'c' => [1, 2, 3],
        ];

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn($request) => $request->getUri()->getQuery() === http_build_query($query)))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->get('/test', $query);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testPost(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) {
                return $request->getUri()->getPath() === '/test' && empty(strval($request->getBody()));
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": false, "message": "error"}'));

        $transport = $this->createTransport($client);

        $this->assertEquals(new Response(200, [], ['status' => false, 'message' => 'error']), $transport->get('/test'));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testPostWithJsonBody(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) {
                return (string) $request->getBody() === '{"a":15,"b":40,"c":[1,2,3]}';
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": false, "message": "error"}'));

        $transport = $this->createTransport($client);
        $transport->post('/test', ['a' => 15, 'b' => 40, 'c' => [1, 2, 3]]);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testPostWithUrlEncodedBody(): void
    {
        $data = ['a' => 15, 'b' => 40, 'c' => [1, 2, 3]];

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) use ($data) {
                return $request->getHeaderLine('Content-Type') === 'application/x-www-form-urlencoded'
                    && strval($request->getBody()) === http_build_query($data);
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": false, "message": "error"}'));

        $transport = $this->createTransport($client);
        $transport->post('/test', $data, ['Content-Type' => 'application/x-www-form-urlencoded']);
    }

    /**
     * @dataProvider queryParametersProvider
     */
    public function testRequestWithQueryParameters(string $method, array $query, string $expectedQuery): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) use ($method, $expectedQuery) {
                return $request->getMethod() === $method && $request->getUri()->getQuery() === $expectedQuery;
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->request($method, '/test', $query);
    }

    /**
     * @dataProvider dataParametersProvider
     */
    public function testRequestWithDataParameters(
        string $method,
        string $contentType,
        array $data,
        string $expectedData
    ): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) use ($contentType, $method, $expectedData) {
                return $request->getHeaderLine('Content-Type') === $contentType
                    && $request->getMethod() === $method && strval($request->getBody()) === $expectedData;
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->request($method, '/test', $data, ['Content-Type' => $contentType]);
    }

    /**
     * @dataProvider clientExceptionsProvider
     */
    public function testWithClientException(string $exceptionClass, string $expectedExceptionClass): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->willThrowException($this->createStub($exceptionClass));

        $this->expectException($expectedExceptionClass);

        $transport = $this->createTransport($client);
        $transport->get('/test');
    }

    /**
     * @dataProvider requestExceptionsProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testWithRequestException(TransferException $e, int $status): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->willThrowException($e);

        $transport = $this->createTransport($client);
        $response = $transport->get('/test');

        $this->assertEquals($status, $response->getStatusCode());
    }

    /**
     * @dataProvider emptyRequestExceptionsProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testWithEmptyRequestException(TransferException $e, string $expectedExceptionClass): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->willThrowException($e);

        $transport = $this->createTransport($client);

        $this->expectException($expectedExceptionClass);

        $transport->get('/test');
    }

    public function testWithInvalidJsonMessage(): void
    {
        $client = $this->createStub(ClientInterface::class);

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->post('/test', ['file' => tmpfile()]);
    }

    public function testWithInvalidJsonResponse(): void
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('send')
            ->willReturn(new PsrResponse(200, [], '{"a:15}'));

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->get('/test');
    }

    /**
     * @testWith ["multipart/form-data"]
     *           ["multipart/form-data"]

     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    public function testWithUnsupportedContentType(string $contentType): void
    {
        $client = $this->createStub(ClientInterface::class);

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->post('/test', ['file' => tmpfile()], ['Content-Type' => $contentType]);
    }

    public function queryParametersProvider(): array
    {
        return [
            [
                'HEAD',
                [],
                '',
            ],
            [
                'GET',
                ['a' => 15, 'b' => 20, 'c' => ['x' => 'str', 'y' => '100', 'c' => 200]],
                'a=15&b=20&c%5Bx%5D=str&c%5By%5D=100&c%5Bc%5D=200',
            ],
            [
                'OPTIONS',
                ['a' => 'one', 'b' => 'two', 'c' => ['a', 'b', 'c']],
                'a=one&b=two&c%5B0%5D=a&c%5B1%5D=b&c%5B2%5D=c',
            ],
            [
                'DELETE',
                ['a' => [1, 2, 3], 'b' => ['a' => 'a', 'b' => 'c'], 'c' => []],
                'a%5B0%5D=1&a%5B1%5D=2&a%5B2%5D=3&b%5Ba%5D=a&b%5Bb%5D=c',
            ],
        ];
    }

    public function dataParametersProvider(): array
    {
        return [
            [
                'POST',
                'application/json',
                ['a' => 15, 'b' => 20, 'c' => ['x' => 'str', 'y' => '100', 'c' => 200]],
                '{"a":15,"b":20,"c":{"x":"str","y":"100","c":200}}',
            ],
            [
                'PUT',
                'application/json',
                ['a' => 'one', 'b' => 'two', 'c' => ['a', 'b', 'c']],
                '{"a":"one","b":"two","c":["a","b","c"]}',
            ],
            [
                'PATCH',
                'application/json',
                [],
                '',
            ],
            [
                'POST',
                'application/x-www-form-urlencoded',
                $q = ['a' => 15, 'b' => 20, 'c' => ['x' => 'str', 'y' => '100', 'c' => 200]],
                http_build_query($q),
            ],
            [
                'PUT',
                'application/x-www-form-urlencoded',
                $q = ['a' => 'one', 'b' => 'two', 'c' => ['a', 'b', 'c']],
                http_build_query($q),
            ],
            [
                'PATCH',
                'application/x-www-form-urlencoded',
                [],
                '',
            ],
        ];
    }

    public function authenticationsProvider(): array
    {
        return [
            [new BearerTokenAuth('test'), 'Authorization', 'Bearer test'],
            [new ApiKeyAuth('test'), 'apiKey', 'test'],
            [new BasicAuth('test', 'test'), 'Authorization', 'Basic ' . base64_encode('test:test')],
        ];
    }

    public function clientExceptionsProvider(): array
    {
        return [
            [ConnectException::class, NetworkException::class],
            [TransferException::class, TransportException::class],
        ];
    }

    public function requestExceptionsProvider(): array
    {
        return [
            [new ClientException('Test', new Request('GET', '/'), new \GuzzleHttp\Psr7\Response(400)), 400],
            [new ServerException('Test', new Request('GET', '/'), new \GuzzleHttp\Psr7\Response(500)), 500],
            [new RequestException('Test', new Request('GET', '/'), new \GuzzleHttp\Psr7\Response(400)), 400],
        ];
    }

    public function emptyRequestExceptionsProvider(): array
    {
        return [
            [new RequestException('Test', new Request('GET', '/')), TransportException::class],
            [new TooManyRedirectsException('Test', new Request('GET', '/')), TransportException::class],
        ];
    }

    private function createTransport(ClientInterface $client): TransportInterface
    {
        return new GuzzleTransport($client);
    }

    public function testLogging(): void
    {
        $client = $this->createStub(ClientInterface::class);
        $client->method('send')
            ->willReturn(new PsrResponse(200, [], '{"a":"15"}'));
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->atLeast(2))
            ->method('debug')
            ->withConsecutive(
                [AbstractTransport::LOGGER_PREFIX . ': Request: POST http://example.com/ {"a":"15"}',[]],
                [AbstractTransport::LOGGER_PREFIX . ': Response: 200 http://example.com/ {"a":"15"}',[]]
            );

        $transport = $this->createTransport($client);

        $transport->setBaseUri('http://example.com');

        $transport->setLogger($logger);

        $transport->post('/',['a'=>'15']);
    }
}
