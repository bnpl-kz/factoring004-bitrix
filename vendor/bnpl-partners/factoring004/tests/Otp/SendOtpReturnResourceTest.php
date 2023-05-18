<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\ClientInterface;

class SendOtpReturnResourceTest extends AbstractResourceTest
{
    /**
     * @testWith [0]
     *           [6000]
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     * @param int $amount
     */
    public function testSendOtpReturn($amount)
    {
        $otp = new SendOtpReturn($amount, '1', '100');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('POST', '/accounting/v1/sendOtpReturn', $otp->toArray(), [])
            ->willReturn(new Response(200, [], ['msg' => 'OK']));

        $resource = new OtpResource($transport, static::BASE_URI);
        $response = $resource->sendOtpReturn($otp);

        $this->assertEquals(new DtoOtp('OK'), $response);
    }

    /**
     * @return void
     */
    protected function callResourceMethod(ClientInterface $client)
    {
        $resource = new OtpResource($this->createTransport($client), static::BASE_URI);
        $resource->sendOtpReturn(new SendOtpReturn(0, '1', '100'));
    }
}
