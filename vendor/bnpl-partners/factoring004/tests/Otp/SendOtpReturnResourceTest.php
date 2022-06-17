<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Psr\Http\Client\ClientInterface;

class SendOtpReturnResourceTest extends AbstractResourceTest
{
    /**
     * @testWith [0]
     *           [6000]
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testSendOtpReturn(int $amount): void
    {
        $otp = new SendOtpReturn($amount, '1', '100');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('POST', '/accountingservice/1.0/sendOtpReturn', $otp->toArray(), [])
            ->willReturn(new Response(200, [], ['msg' => 'OK']));

        $resource = new OtpResource($transport, static::BASE_URI);
        $response = $resource->sendOtpReturn($otp);

        $this->assertEquals(new DtoOtp('OK'), $response);
    }

    protected function callResourceMethod(ClientInterface $client): void
    {
        $resource = new OtpResource($this->createTransport($client), static::BASE_URI);
        $resource->sendOtpReturn(new SendOtpReturn(0, '1', '100'));
    }
}
