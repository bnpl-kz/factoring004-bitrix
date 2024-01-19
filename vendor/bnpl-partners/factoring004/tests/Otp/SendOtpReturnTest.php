<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Otp;

use PHPUnit\Framework\TestCase;

class SendOtpReturnTest extends TestCase
{
    public function testCreateFromArray(): void
    {
        $expected = new SendOtpReturn(0, 'test', '1000');
        $actual = SendOtpReturn::createFromArray(['amountAR' => 0, 'merchantId' => 'test', 'merchantOrderId' => '1000']);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAmountAr(): void
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals(0, $SendOtpReturn->getAmountAr());

        $SendOtpReturn = new SendOtpReturn(6000, 'other', '1000');
        $this->assertEquals(6000, $SendOtpReturn->getAmountAr());
    }

    public function testGetMerchantId(): void
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals('test', $SendOtpReturn->getMerchantId());

        $SendOtpReturn = new SendOtpReturn(0, 'other', '1000');
        $this->assertEquals('other', $SendOtpReturn->getMerchantId());
    }

    public function testGetMerchantOrderId(): void
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals('1000', $SendOtpReturn->getMerchantOrderId());

        $SendOtpReturn = new SendOtpReturn(0, 'other', '2000');
        $this->assertEquals('2000', $SendOtpReturn->getMerchantOrderId());
    }

    public function testToArray(): void
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $expected = ['amountAR' => 0, 'merchantId' => 'test', 'merchantOrderId' => '1000'];
        $this->assertEquals($expected, $SendOtpReturn->toArray());

        $SendOtpReturn = new SendOtpReturn(6000, 'shop', '2000');
        $expected = ['amountAR' => 6000, 'merchantId' => 'shop', 'merchantOrderId' => '2000'];
        $this->assertEquals($expected, $SendOtpReturn->toArray());
    }
}

