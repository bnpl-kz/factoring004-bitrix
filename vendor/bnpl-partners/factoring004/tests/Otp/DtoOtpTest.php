<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractTestCase;

class DtoOtpTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
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

    /**
     * @return void
     */
    public function testGetMsg()
    {
        $otp = new DtoOtp('test');
        $this->assertEquals('test', $otp->getMsg());

        $otp = new DtoOtp('message');
        $this->assertEquals('message', $otp->getMsg());
    }

    /**
     * @return void
     */
    public function testIsError()
    {
        $otp = new DtoOtp('test');
        $this->assertFalse($otp->isError());

        $otp = new DtoOtp('message', true);
        $this->assertTrue($otp->isError());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $otp = new DtoOtp('test');
        $this->assertEquals(['msg' => 'test', 'error' => false], $otp->toArray());

        $otp = new DtoOtp('message', true);
        $this->assertEquals(['msg' => 'message', 'error' => true], $otp->toArray());
    }

    /**
     * @return void
     */
    public function testJsonSerialize()
    {
        $otp = new DtoOtp('test');
        $this->assertJsonStringEqualsJsonString('{"msg":"test","error":false}', json_encode($otp));

        $otp = new DtoOtp('message', true);
        $this->assertJsonStringEqualsJsonString('{"msg":"message","error":true}', json_encode($otp));
    }
}

