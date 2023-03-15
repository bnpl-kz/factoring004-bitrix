<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Otp;

use PHPUnit\Framework\TestCase;

class DtoOtpTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new DtoOtp('test');
        $actual = DtoOtp::createFromArray(['msg' => 'test']);
        $this->assertEquals($expected, $actual);

        $expected = new DtoOtp('test', true);
        $actual = DtoOtp::createFromArray(['msg' => 'test', 'error' => true]);
        $this->assertEquals($expected, $actual);

        $expected = new DtoOtp('test');
        $actual = DtoOtp::createFromArray(['msg' => 'test', 'error' => 'false']);
        $this->assertEquals($expected, $actual);

        $expected = new DtoOtp('test', true);
        $actual = DtoOtp::createFromArray(['msg' => 'test', 'error' => 'true']);
        $this->assertEquals($expected, $actual);
    }

    public function testGetMsg(): void
    {
        $otp = new DtoOtp('test');
        $this->assertEquals('test', $otp->getMsg());

        $otp = new DtoOtp('message');
        $this->assertEquals('message', $otp->getMsg());
    }

    public function testIsError(): void
    {
        $otp = new DtoOtp('test');
        $this->assertFalse($otp->isError());

        $otp = new DtoOtp('message', true);
        $this->assertTrue($otp->isError());
    }

    public function testToArray(): void
    {
        $otp = new DtoOtp('test');
        $this->assertEquals(['msg' => 'test', 'error' => false], $otp->toArray());

        $otp = new DtoOtp('message', true);
        $this->assertEquals(['msg' => 'message', 'error' => true], $otp->toArray());
    }

    public function testJsonSerialize(): void
    {
        $otp = new DtoOtp('test');
        $this->assertJsonStringEqualsJsonString('{"msg":"test","error":false}', json_encode($otp));

        $otp = new DtoOtp('message', true);
        $this->assertJsonStringEqualsJsonString('{"msg":"message","error":true}', json_encode($otp));
    }
}

