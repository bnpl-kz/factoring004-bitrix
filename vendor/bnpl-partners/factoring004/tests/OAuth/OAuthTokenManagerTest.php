<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\OAuth;

use BadMethodCallException;
use BnplPartners\Factoring004\Exception\OAuthException;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class OAuthTokenManagerTest extends TestCase
{
    public function testWithEmptyBaseUri(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('', 'test', 'test');
    }

    public function testWithEmptyUsername(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('http://example.com', '', 'test');
    }

    public function testWithEmptyPassword(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OAuthTokenManager('http://example.com', 'test', '');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testGetAccessToken(): void
    {
        $username = 'test';
        $password = 'password';
        $data = compact('username', 'password');
        $responseData = [
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ];

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) use ($data) {
                return $request->getMethod() === 'POST'
                    && $request->getUri()->getAuthority() === 'example.com'
                    && $request->getUri()->getScheme() === 'http'
                    && $request->getUri()->getPath() === OAuthTokenManager::ACCESS_PATH
                    && $request->getHeaderLine('Content-Type') === 'application/json'
                    && strval($request->getBody()) === json_encode($data);
            }))
            ->willReturn(new Response(200, [], json_encode($responseData)));

        $manager = new OAuthTokenManager(
            'http://example.com',
            $username,
            $password,
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
            ->method('send')
            ->withAnyParameters()
            ->willThrowException($this->createStub(TransferException::class));

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
            ->method('send')
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
    public function testRefreshToken(): void
    {
        $refreshToken = 'dG9rZW4=';
        $responseData = [
            'access' => 'dGVzdA==',
            'accessExpiresAt' => 300,
            'refresh' => 'dGVzdDE=',
            'refreshExpiresAt' => 3600,
        ];

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(function (RequestInterface $request) use ($refreshToken) {
                return $request->getMethod() === 'POST'
                    && $request->getUri()->getAuthority() === 'example.com'
                    && $request->getUri()->getScheme() === 'http'
                    && $request->getUri()->getPath() === OAuthTokenManager::REFRESH_PATH
                    && $request->getHeaderLine('Content-Type') === 'application/json'
                    && strval($request->getBody()) === json_encode(compact('refreshToken'));
            }))
            ->willReturn(new Response(200, [], json_encode($responseData)));

        $manager = new OAuthTokenManager(
            'http://example.com',
            'test',
            'password',
            $this->createTransport($client),
        );

        $this->assertEquals(OAuthToken::createFromArray($responseData), $manager->refreshToken($refreshToken));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRefreshTokenFailed(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->withAnyParameters()
            ->willThrowException($this->createStub(TransferException::class));

        $manager = new OAuthTokenManager(
            'http://example.com',
            'test',
            'password',
            $this->createTransport($client),
        );

        $this->expectException(OAuthException::class);
        $manager->refreshToken('dG9rZW4=');
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\OAuthException
     */
    public function testRefreshTokenFailedWithUnexpectedResponse(): void
    {
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('send')
            ->withAnyParameters()
            ->willReturn(new Response(400, [], json_encode([])));

        $manager = new OAuthTokenManager(
            'http://example.com',
            'test',
            'password',
            $this->createTransport($client),
        );

        $this->expectException(OAuthException::class);
        $manager->refreshToken('dG9rZW4=');
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
        return new GuzzleTransport($client);
    }
}

