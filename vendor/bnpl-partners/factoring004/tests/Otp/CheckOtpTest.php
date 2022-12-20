<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractTestCase;

class CheckOtpTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new CheckOtp('test', '1000', 'test', 6000);
        $actual = CheckOtp::createFromArray(['merchantId' => 'test', 'merchantOrderId' => '1000', 'otp' => 'test', 'amount' => 6000]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetMerchantId()
    {
        $checkOtp = new CheckOtp('test', '1000', 'test', 6000);
        $this->assertEquals('test', $checkOtp->getMerchantId());

        $checkOtp = new CheckOtp('other', '1000', 'test', 6000);
        $this->assertEquals('other', $checkOtp->getMerchantId());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $checkOtp = new CheckOtp('test', '1000', 'test', 6000);
        $this->assertEquals('1000', $checkOtp->getMerchantOrderId());

        $checkOtp = new CheckOtp('other', '2000', 'test', 6000);
        $this->assertEquals('2000', $checkOtp->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testGetOtp()
    {
        $checkOtp = new CheckOtp('test', '1000', 'test', 6000);
        $this->assertEquals('test', $checkOtp->getOtp());

        $checkOtp = new CheckOtp('other', '2000', 'another', 6000);
        $this->assertEquals('another', $checkOtp->getOtp());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $checkOtp = new CheckOtp('test', '1000', 'test', 6000);
        $expected = ['merchantId' => 'test', 'merchantOrderId' => '1000', 'otp' => 'test', 'amount' => 6000];
        $this->assertEquals($expected, $checkOtp->toArray());

        $checkOtp = new CheckOtp('shop', '1000', 'other', 6000);
        $expected = ['merchantId' => 'shop', 'merchantOrderId' => '1000', 'otp' => 'other', 'amount' => 6000];
        $this->assertEquals($expected, $checkOtp->toArray());
    }
}

