<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004;

use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\PreApp\PreAppResource;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private const BASE_URI = 'http://example.com';

    public function testCreate(): void
    {
        $transport = $this->createStub(TransportInterface::class);

        $expected = new Api(static::BASE_URI, null, $transport);
        $actual = Api::create(static::BASE_URI, null, $transport);

        $this->assertEquals($expected, $actual);
    }

    public function testCreateWithDefaultClient(): void
    {
        $expected = new Api(static::BASE_URI);
        $actual = Api::create(static::BASE_URI);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @testWith [""]
     *           ["http"]
     *           ["https"]
     *           ["http:"]
     *           ["https:"]
     *           ["http://"]
     *           ["https://"]
     *           ["example"]
     *           ["/path"]
     */
    public function testCreateWithEmptyBaseUri(string $baseUri): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Api($baseUri);
    }

    public function testPreApps(): void
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(PreAppResource::class, $api->preApps);
        $this->assertSame($api->preApps, $api->preApps);
    }

    public function testGetUnexpectedProperty(): void
    {
        $api = new Api(static::BASE_URI);

        $this->expectException(OutOfBoundsException::class);

        $this->assertNull($api->test);
    }

    public function testOtp(): void
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(OtpResource::class, $api->otp);
        $this->assertSame($api->otp, $api->otp);
    }

    public function testChangeStatus(): void
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(ChangeStatusResource::class, $api->changeStatus);
        $this->assertSame($api->changeStatus, $api->changeStatus);
    }
}

