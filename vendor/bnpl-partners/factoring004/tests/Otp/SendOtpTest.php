<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Otp;

use PHPUnit\Framework\TestCase;

class SendOtpTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new SendOtp('test', '1000', 6000);
        $actual = SendOtp::createFromArray(['merchantId' => 'test', 'merchantOrderId' => '1000', 'amount' => 6000]);

        $this->assertEquals($expected, $actual);
    }

    public function testGetMerchantId(): void
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $this->assertEquals('test', $sendOtp->getMerchantId());

        $sendOtp = new SendOtp('other', '1000', 6000);
        $this->assertEquals('other', $sendOtp->getMerchantId());
    }

    public function testGetMerchantOrderId(): void
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $this->assertEquals('1000', $sendOtp->getMerchantOrderId());

        $sendOtp = new SendOtp('other', '2000', 6000);
        $this->assertEquals('2000', $sendOtp->getMerchantOrderId());
    }

    public function testToArray(): void
    {
        $sendOtp = new SendOtp('test', '1000', 6000);
        $expected = ['merchantId' => 'test', 'merchantOrderId' => '1000', 'amount' => 6000];
        $this->assertEquals($expected, $sendOtp->toArray());

        $sendOtp = new SendOtp('shop', '2000', 6000);
        $expected = ['merchantId' => 'shop', 'merchantOrderId' => '2000', 'amount' => 6000];
        $this->assertEquals($expected, $sendOtp->toArray());
    }
}

