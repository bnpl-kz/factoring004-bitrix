<?php

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Auth\ApiKeyAuth;
use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\Auth\BasicAuth;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response as PsrResponse;
use BnplPartners\Factoring004\AbstractTestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * @requires PHP 7.2
 */
class PsrTransportTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testBaseUriIsEmpty()
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return (string) $request->getUri() === '/';
            }))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testSetBaseUri()
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return (string) $request->getUri() === 'http://example.com/';
            }))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setBaseUri('http://example.com');
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testSetBaseUriWithPath()
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return (string) $request->getUri() === 'http://example.com/1.0/preapp';
            }))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setBaseUri('http://example.com/1.0');
        $transport->get('/preapp');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testHeadersIsEmpty()
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return empty($request->getHeaders());
            }))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testSetHeaders()
    {
        $client = $this->createMock(ClientInterface::class);

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Test',
        ];

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) use ($headers) {
                return $request->getHeaders() === array_map(function ($item) {
                    return [$item];
                }, $headers);
            }))
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setHeaders($headers);
        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testOverrideHeaders()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return $request->getHeaders() === [
                        'Content-Type' => ['application/x-www-form-urlencoded'],
                        'Accept' => ['application/json'],
                    ];
            })
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
     * @return void
     * @param string $expectedHeaderName
     * @param string $expectedHeaderValue
     */
    public function testSetAuthentication(
        AuthenticationInterface $authentication,
        $expectedHeaderName,
        $expectedHeaderValue
    ) {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with(
                $this->callback(function ($request) use ($expectedHeaderName, $expectedHeaderValue) {
                    return $request->getHeaderLine($expectedHeaderName) === $expectedHeaderValue;
                })
            )
            ->willReturn(new PsrResponse(200, [], '{}'));

        $transport = $this->createTransport($client);
        $transport->setAuthentication($authentication);

        $transport->get('/');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testGet()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) {
                return $request->getUri()->getPath() === '/test';
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);

        $this->assertEquals(new Response(200, [], ['status' => true, 'message' => 'text']), $transport->get('/test'));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testGetWithQuery()
    {
        $client = $this->createMock(ClientInterface::class);
        $query = [
            'a' => 15,
            'b' => 40,
            'c' => [1, 2, 3],
        ];

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function ($request) use ($query) {
                return $request->getUri()->getQuery() === http_build_query($query);
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->get('/test', $query);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testPost()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                return $request->getUri()->getPath() === '/test' && empty(strval($request->getBody()));
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": false, "message": "error"}'));

        $transport = $this->createTransport($client);

        $this->assertEquals(new Response(200, [], ['status' => false, 'message' => 'error']), $transport->get('/test'));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testPostWithJsonBody()
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) {
                return strval($request->getBody()) === '{"a":15,"b":40,"c":[1,2,3]}';
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": false, "message": "error"}'));

        $transport = $this->createTransport($client);
        $transport->post('/test', ['a' => 15, 'b' => 40, 'c' => [1, 2, 3]]);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     */
    public function testPostWithUrlEncodedBody()
    {
        $data = ['a' => 15, 'b' => 40, 'c' => [1, 2, 3]];

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
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
     * @return void
     * @param string $method
     * @param string $expectedQuery
     */
    public function testRequestWithQueryParameters($method, array $query, $expectedQuery)
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($method, $expectedQuery) {
                return $request->getMethod() === $method && $request->getUri()->getQuery() === $expectedQuery;
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->request($method, '/test', $query);
    }

    /**
     * @dataProvider dataParametersProvider
     * @return void
     * @param string $method
     * @param string $contentType
     * @param string $expectedData
     */
    public function testRequestWithDataParameters(
        $method,
        $contentType,
        array $data,
        $expectedData
    )
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($contentType, $method, $expectedData) {
                return $request->getHeaderLine('Content-Type') === $contentType
                    && $request->getMethod() === $method && strval($request->getBody()) === $expectedData;
            }))
            ->willReturn(new PsrResponse(200, [], '{"status": true, "message": "text"}'));

        $transport = $this->createTransport($client);
        $transport->request($method, '/test', $data, ['Content-Type' => $contentType]);
    }

    /**
     * @dataProvider psrClientExceptionsProvider
     * @return void
     * @param string $exceptionClass
     * @param string $expectedExceptionClass
     */
    public function testWithClientException($exceptionClass, $expectedExceptionClass)
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willThrowException($this->createStub($exceptionClass));

        $this->expectException($expectedExceptionClass);

        $transport = $this->createTransport($client);
        $transport->get('/test');
    }

    /**
     * @return void
     */
    public function testWithInvalidJsonMessage()
    {
        $client = $this->createStub(ClientInterface::class);

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->post('/test', ['file' => tmpfile()]);
    }

    /**
     * @return void
     */
    public function testWithInvalidJsonResponse()
    {
        $client = $this->createMock(ClientInterface::class);

        $client->expects($this->once())
            ->method('sendRequest')
            ->willReturn(new PsrResponse(200, [], '{"a:15}'));

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->get('/test');
    }

    /**
     * @testWith ["multipart/form-data"]
     *           ["multipart/form-data"]
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return void
     * @param string $contentType
     */
    public function testWithUnsupportedContentType($contentType)
    {
        $client = $this->createStub(ClientInterface::class);

        $this->expectException(DataSerializationException::class);

        $transport = $this->createTransport($client);
        $transport->post('/test', ['file' => tmpfile()], ['Content-Type' => $contentType]);
    }

    /**
     * @return mixed[]
     */
    public function queryParametersProvider()
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

    /**
     * @return mixed[]
     */
    public function dataParametersProvider()
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

    /**
     * @return mixed[]
     */
    public function authenticationsProvider()
    {
        return [
            [new BearerTokenAuth('test'), 'Authorization', 'Bearer test'],
            [new ApiKeyAuth('test'), 'apiKey', 'test'],
            [new BasicAuth('test', 'test'), 'Authorization', 'Basic ' . base64_encode('test:test')],
        ];
    }

    /**
     * @return mixed[]
     */
    public function psrClientExceptionsProvider()
    {
        return [
            [NetworkExceptionInterface::class, NetworkException::class],
            [RequestExceptionInterface::class, TransportException::class],
            [ClientExceptionInterface::class, TransportException::class],
        ];
    }

    /**
     * @return \BnplPartners\Factoring004\Transport\TransportInterface
     */
    private function createTransport(ClientInterface $client)
    {
        return new PsrTransport(new HttpFactory(), new HttpFactory(), new HttpFactory(), $client);
    }

    /**
     * @return void
     */
    public function testLogging()
    {
        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
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
