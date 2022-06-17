<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Transport\Response;
use BnplPartners\Factoring004\Transport\TransportInterface;
use Psr\Http\Client\ClientInterface;

class CheckOtpReturnResourceTest extends AbstractResourceTest
{
    /**
     * @testWith [0]
     *           [6000]
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     */
    public function testCheckOtpReturn(int $amount): void
    {
        $otp = new CheckOtpReturn($amount, '100', 'test', '1234');

        $transport = $this->createMock(TransportInterface::class);
        $transport->expects($this->once())
            ->method('request')
            ->with('POST', '/accountingservice/1.0/checkOtpReturn', $otp->toArray(), [])
            ->willReturn(new Response(200, [], ['msg' => 'OK']));

        $resource = new OtpResource($transport, static::BASE_URI);
        $response = $resource->checkOtpReturn($otp);

        $this->assertEquals(new DtoOtp('OK'), $response);
    }

    protected function callResourceMethod(ClientInterface $client): void
    {
        $resource = new OtpResource($this->createTransport($client), static::BASE_URI);
        $resource->checkOtpReturn(new CheckOtpReturn(0, '1', '100', '1234'));
    }
}
