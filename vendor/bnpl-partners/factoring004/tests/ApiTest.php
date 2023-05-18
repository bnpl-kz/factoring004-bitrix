<?php

namespace BnplPartners\Factoring004;

use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\PreApp\PreAppResource;
use BnplPartners\Factoring004\Transport\TransportInterface;
use InvalidArgumentException;
use OutOfBoundsException;
use BnplPartners\Factoring004\AbstractTestCase;

class ApiTest extends AbstractTestCase
{
    const BASE_URI = 'http://example.com';

    /**
     * @return void
     */
    public function testCreate()
    {
        $transport = $this->createStub(TransportInterface::class);

        $expected = new Api(static::BASE_URI, null, $transport);
        $actual = Api::create(static::BASE_URI, null, $transport);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testCreateWithDefaultClient()
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
     * @return void
     * @param string $baseUri
     */
    public function testCreateWithEmptyBaseUri($baseUri)
    {
        $this->expectException(InvalidArgumentException::class);

        new Api($baseUri);
    }

    /**
     * @return void
     */
    public function testPreApps()
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(PreAppResource::class, $api->preApps);
        $this->assertSame($api->preApps, $api->preApps);
    }

    /**
     * @return void
     */
    public function testGetUnexpectedProperty()
    {
        $api = new Api(static::BASE_URI);

        $this->expectException(OutOfBoundsException::class);

        $this->assertNull($api->test);
    }

    /**
     * @return void
     */
    public function testOtp()
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(OtpResource::class, $api->otp);
        $this->assertSame($api->otp, $api->otp);
    }

    /**
     * @return void
     */
    public function testChangeStatus()
    {
        $api = new Api(static::BASE_URI);

        $this->assertInstanceOf(ChangeStatusResource::class, $api->changeStatus);
        $this->assertSame($api->changeStatus, $api->changeStatus);
    }
}

