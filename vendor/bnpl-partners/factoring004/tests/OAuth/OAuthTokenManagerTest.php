<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BadMethodCallException;
use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\Transport\PsrTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class OAuthTokenManagerTest extends TestCase
{
    public function testWithEmptyBaseUri(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('', 'test', 'test');
    }

    public function testWithEmptyConsumerKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('http://example.com', '', 'test');
    }

    public function testWithEmptyConsumerSecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('http://example.com', 'test', '');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessToken(): void
    {
        $consumerKey = 'a62f2225bf70bfaccbc7f1ef2a397836717377de';
        $consumerSecret = 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4';
        $data = ['grant_type' => 'client_credentials'];
        $responseData = [
            'access_token' => 'dGVzdA==',
            'scope' => 'default',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ];

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->with($this->callback(function (RequestInterface $request) use ($consumerSecret, $consumerKey, $data) {
                return $request->getMethod() === 'POST'
                    && $request->getUri()->getAuthority() === 'example.com'
                    && $request->getUri()->getScheme() === 'http'
                    && $request->getUri()->getPath() === '/token'
                    && $request->getHeaderLine('Authorization') === 'Basic ' . base64_encode(
                        $consumerKey . ':' . $consumerSecret
                    )
                    && $request->getHeaderLine('Content-Type') === 'application/x-www-form-urlencoded'
                    && strval($request->getBody()) === http_build_query($data);
            }))
            ->willReturn(new Response(200, [], json_encode($responseData)));

        $manager = new OAuthTokenManager(
            'http://example.com',
            $consumerKey,
            $consumerSecret,
            $this->createTransport($client),
        );

        $this->assertEquals(OAuthToken::createFromArray($responseData), $manager->getAccessToken());
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenFailed(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->withAnyParameters()
            ->willThrowException($this->createStub(ClientExceptionInterface::class));

        $manager = new OAuthTokenManager(
            'http://example.com',
            'a62f2225bf70bfaccbc7f1ef2a397836717377de',
            'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4',
            $this->createTransport($client),
        );

        $this->expectException(OAuthException::class);
        $manager->getAccessToken();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessTokenFailedWithUnexpectedResponse(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('sendRequest')
            ->withAnyParameters()
            ->willReturn(new Response(400, [], json_encode([])));

        $manager = new OAuthTokenManager(
            'http://example.com',
            'a62f2225bf70bfaccbc7f1ef2a397836717377de',
            'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4',
            $this->createTransport($client),
        );

        $this->expectException(OAuthException::class);
        $manager->getAccessToken();
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRevokeToken(): void
    {
        $manager = new OAuthTokenManager(
            'http://example.com',
            'a62f2225bf70bfaccbc7f1ef2a397836717377de',
            'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4'
        );

        $this->expectException(BadMethodCallException::class);

        $manager->revokeToken();
    }

    public function createTransport(ClientInterface $client): TransportInterface
    {
        return new PsrTransport(
            new HttpFactory(),
            new HttpFactory(),
            new HttpFactory(),
            $client
        );
    }
}

