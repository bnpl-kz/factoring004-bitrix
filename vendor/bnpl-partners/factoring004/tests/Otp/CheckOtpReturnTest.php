<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractTestCase;

class CheckOtpReturnTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new CheckOtpReturn(0, 'test', '1000', 'test');
        $actual = CheckOtpReturn::createFromArray([
            'amountAR' => 0,
            'merchantId' => 'test',
            'merchantOrderId' => '1000',
            'otp' => 'test',
        ]);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetAmountAr()
    {
        $CheckOtpReturn = new CheckOtpReturn(0, 'test', '1000', 'test');
        $this->assertEquals(0, $CheckOtpReturn->getAmountAr());

        $CheckOtpReturn = new CheckOtpReturn(6000, 'other', '1000', 'test');
        $this->assertEquals(6000, $CheckOtpReturn->getAmountAr());
    }

    /**
     * @return void
     */
    public function testGetMerchantId()
    {
        $CheckOtpReturn = new CheckOtpReturn(0, 'test', '1000', 'test');
        $this->assertEquals('test', $CheckOtpReturn->getMerchantId());

        $CheckOtpReturn = new CheckOtpReturn(0, 'other', '1000', 'test');
        $this->assertEquals('other', $CheckOtpReturn->getMerchantId());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $CheckOtpReturn = new CheckOtpReturn(0, 'test', '1000', 'test');
        $this->assertEquals('1000', $CheckOtpReturn->getMerchantOrderId());

        $CheckOtpReturn = new CheckOtpReturn(0, 'other', '2000', 'test');
        $this->assertEquals('2000', $CheckOtpReturn->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testGetOtp()
    {
        $CheckOtpReturn = new CheckOtpReturn(0, 'test', '1000', 'test');
        $this->assertEquals('test', $CheckOtpReturn->getOtp());

        $CheckOtpReturn = new CheckOtpReturn(0, 'other', '2000', 'another');
        $this->assertEquals('another', $CheckOtpReturn->getOtp());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $CheckOtpReturn = new CheckOtpReturn(0, 'test', '1000', 'test');
        $expected = ['amountAR' => 0, 'merchantId' => 'test', 'merchantOrderId' => '1000', 'otp' => 'test'];
        $this->assertEquals($expected, $CheckOtpReturn->toArray());

        $CheckOtpReturn = new CheckOtpReturn(6000, 'shop', '1000', 'other');
        $expected = ['amountAR' => 6000, 'merchantId' => 'shop', 'merchantOrderId' => '1000', 'otp' => 'other'];
        $this->assertEquals($expected, $CheckOtpReturn->toArray());
    }
}

