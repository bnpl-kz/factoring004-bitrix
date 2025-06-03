<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractTestCase;

class SendOtpReturnTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new SendOtpReturn(0, 'test', '1000');
        $actual = SendOtpReturn::createFromArray(['amountAR' => 0, 'merchantId' => 'test', 'merchantOrderId' => '1000']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function testGetAmountAr()
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals(0, $SendOtpReturn->getAmountAr());

        $SendOtpReturn = new SendOtpReturn(6000, 'other', '1000');
        $this->assertEquals(6000, $SendOtpReturn->getAmountAr());
    }

    /**
     * @return void
     */
    public function testGetMerchantId()
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals('test', $SendOtpReturn->getMerchantId());

        $SendOtpReturn = new SendOtpReturn(0, 'other', '1000');
        $this->assertEquals('other', $SendOtpReturn->getMerchantId());
    }

    /**
     * @return void
     */
    public function testGetMerchantOrderId()
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $this->assertEquals('1000', $SendOtpReturn->getMerchantOrderId());

        $SendOtpReturn = new SendOtpReturn(0, 'other', '2000');
        $this->assertEquals('2000', $SendOtpReturn->getMerchantOrderId());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $SendOtpReturn = new SendOtpReturn(0, 'test', '1000');
        $expected = ['amountAR' => 0, 'merchantId' => 'test', 'merchantOrderId' => '1000'];
        $this->assertEquals($expected, $SendOtpReturn->toArray());

        $SendOtpReturn = new SendOtpReturn(6000, 'shop', '2000');
        $expected = ['amountAR' => 6000, 'merchantId' => 'shop', 'merchantOrderId' => '2000'];
        $this->assertEquals($expected, $SendOtpReturn->toArray());
    }
}

