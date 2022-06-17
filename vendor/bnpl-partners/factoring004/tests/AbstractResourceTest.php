<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004;

use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\PsrTransport;
use BnplPartners\Factoring004\Transport\Response as TransportResponse;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;

abstract class AbstractResourceTest extends TestCase
{
    protected const BASE_URI = 'http://example.com';

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithMethodNotAllowedError(): void
    {
        $data = [
            'code' => '405',
            'type' => 'Status report',
            'message' => 'Runtime Error',
            'description' => 'Method not allowed for given API resource',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(405, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals(405, $e->getCode());
            $this->assertEquals('Runtime Error', $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data), $e->getErrorResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithMethodNotAllowedFault(): void
    {
        $data = [
            'fault' => [
                'code' => '405',
                'type' => 'Status report',
                'message' => 'Runtime Error',
                'description' => 'Method not allowed for given API resource',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(405, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals(405, $e->getCode());
            $this->assertEquals('Runtime Error', $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data['fault']), $e->getErrorResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithMissingCredentialsError(): void
    {
        $data = [
            'code' => '900902',
            'message' => 'Missing Credentials',
            'description' => 'Invalid Credentials. Make sure your API invocation call has a header',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['code'], $e->getCode());
            $this->assertEquals($data['message'], $e->getMessage());
            $this->assertEquals($data['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithMissingCredentialsFault(): void
    {
        $data = [
            'fault' => [
                'code' => '900902',
                'message' => 'Missing Credentials',
                'description' => 'Invalid Credentials. Make sure your API invocation call has a header',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['fault']['code'], $e->getCode());
            $this->assertEquals($data['fault']['message'], $e->getMessage());
            $this->assertEquals($data['fault']['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithInvalidCredentialsError(): void
    {
        $data = [
            'code' => '900901',
            'message' => 'Invalid Credentials',
            'description' => 'Invalid Credentials. Make sure you have provided the correct security credentials',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['code'], $e->getCode());
            $this->assertEquals($data['message'], $e->getMessage());
            $this->assertEquals($data['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithInvalidCredentialsFault(): void
    {
        $data = [
            'fault' => [
                'code' => '900901',
                'message' => 'Invalid Credentials',
                'description' => 'Invalid Credentials. Make sure you have provided the correct security credentials',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['fault']['code'], $e->getCode());
            $this->assertEquals($data['fault']['message'], $e->getMessage());
            $this->assertEquals($data['fault']['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithAccessTokenNotAllowError(): void
    {
        $data = [
            'code' => '900910',
            'message' => 'The access token does not allow you to access the requested resource',
            'description' => 'The access token does not allow you to access the requested resource',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['code'], $e->getCode());
            $this->assertEquals($data['message'], $e->getMessage());
            $this->assertEquals($data['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithAccessTokenNotAllowFault(): void
    {
        $data = [
            'fault' => [
                'code' => '900910',
                'message' => 'The access token does not allow you to access the requested resource',
                'description' => 'The access token does not allow you to access the requested resource',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(401, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (AuthenticationException $e) {
            $this->assertEquals((int) $data['fault']['code'], $e->getCode());
            $this->assertEquals($data['fault']['message'], $e->getMessage());
            $this->assertEquals($data['fault']['description'], $e->getDescription());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithForbiddenError(): void
    {
        $data = [
            'code' => '900908',
            'message' => 'Resource forbidden ',
            'description' => 'User is NOT authorized to access the Resource. API Subscription validation failed.',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(403, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals((int) $data['code'], $e->getCode());
            $this->assertEquals($data['message'], $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data), $e->getErrorResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithForbiddenFault(): void
    {
        $data = [
            'fault' => [
                'code' => '900908',
                'message' => 'Resource forbidden ',
                'description' => 'User is NOT authorized to access the Resource. API Subscription validation failed.',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(403, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals((int) $data['fault']['code'], $e->getCode());
            $this->assertEquals($data['fault']['message'], $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data['fault']), $e->getErrorResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithUnexpectedSchemaError(): void
    {
        $data = [
            'error' => [
                'code' => 3,
                'message' => 'proto: syntax error (line 1:16): unexpected token [',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(400, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals($data['error']['code'], $e->getCode());
            $this->assertEquals($data['error']['message'], $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data['error']), $e->getErrorResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testWithUnexpectedSchemaFault(): void
    {
        $data = [
            'fault' => [
                'code' => 3,
                'message' => 'proto: syntax error (line 1:16): unexpected token [',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('sendRequest')
            ->willReturn(new Response(400, ['Content-Type' => 'application/json'], json_encode($data)));

        try {
            $this->callResourceMethod($client);
        } catch (ErrorResponseException $e) {
            $this->assertEquals($data['fault']['code'], $e->getCode());
            $this->assertEquals($data['fault']['message'], $e->getMessage());
            $this->assertEquals(ErrorResponse::createFromArray($data['fault']), $e->getErrorResponse());
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param class-string<\BnplPartners\Factoring004\Exception\UnexpectedResponseException> $exceptionClass
     *
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @testWith [400, {"message": "Error"}, "BnplPartners\\Factoring004\\Exception\\UnexpectedResponseException"]
     *           [400, {}, "BnplPartners\\Factoring004\\Exception\\UnexpectedResponseException"]
     *           [500, {"message": "Error"}, "BnplPartners\\Factoring004\\Exception\\EndpointUnavailableException"]
     *           [500, {}, "BnplPartners\\Factoring004\\Exception\\EndpointUnavailableException"]
     */
    public function testWithUnexpectedError(int $status, array $data, string $exceptionClass): void
    {
        $client = $this->createStub(ClientInterface::class);
        $response = new Response($status, ['Content-Type' => 'application/json'], json_encode($data));

        $client->method('sendRequest')->willReturn($response);

        try {
            $this->callResourceMethod($client);
        } catch (UnexpectedResponseException $e) {
            $this->assertInstanceOf($exceptionClass, $e);

            $this->assertEquals(TransportResponse::createFromPsrResponse($response), $e->getResponse());
        }
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     */
    abstract protected function callResourceMethod(ClientInterface $client): void;

    protected function createTransport(ClientInterface $client): TransportInterface
    {
        return new PsrTransport(
            new HttpFactory(),
            new HttpFactory(),
            new HttpFactory(),
            $client,
        );
    }
}
