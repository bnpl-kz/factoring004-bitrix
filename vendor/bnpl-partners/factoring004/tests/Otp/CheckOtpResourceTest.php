<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use GuzzleHttp\ClientInterface;

class CheckOtpResourceTest extends AbstractResourceTest
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testCheckOtp()
    {
        $otp = new CheckOtp('1', '100', 'test', 6000);

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('POST', '/accounting/checkOtp', $otp->toArray(), [])
            ->willReturn(new Response(200, [], ['msg' => 'OK']));

        $resource = new OtpResource($transport, static::BASE_URI);
        $response = $resource->checkOtp($otp);

        $this->assertEquals(new DtoOtp('OK'), $response);
    }

    /**
     * @return void
     */
    protected function callResourceMethod(ClientInterface $client)
    {
        $resource = new OtpResource($this->createTransport($client), static::BASE_URI);
        $resource->checkOtp(new CheckOtp('1', '100', 'test', 6000));
    }
}
