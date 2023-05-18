<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractTestCase;

class SendOtpTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new SendOtp('test', '1000', 6000);
        $actual = SendOtp::createFromArray(['merchantId' => 'test', 'merchantOrderId' => '1000', 'amount' => 6000]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetMerchantId()
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $this->assertEquals('test', $sendOtp->getMerchantId());

        $sendOtp = new SendOtp('other', '1000', 6000);
        $this->assertEquals('other', $sendOtp->getMerchantId());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $this->assertEquals('1000', $sendOtp->getMerchantOrderId());

        $sendOtp = new SendOtp('other', '2000', 6000);
        $this->assertEquals('2000', $sendOtp->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $expected = ['merchantId' => 'test', 'merchantOrderId' => '1000', 'amount' => 6000];
        $this->assertEquals($expected, $sendOtp->toArray());

        $sendOtp = new SendOtp('shop', '2000', 6000);
        $expected = ['merchantId' => 'shop', 'merchantOrderId' => '2000', 'amount' => 6000];
        $this->assertEquals($expected, $sendOtp->toArray());
    }
}

