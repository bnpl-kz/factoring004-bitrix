<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractResourceTest;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Response\PreAppResponse;
use BnplPartners\Factoring004\Response\ValidationErrorResponse;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;

class PreAppResourceTest extends AbstractResourceTest
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testPreApp()
    {
        $data = [
            'preappId' => '102dbb8f-ca4e-7cad-a3f2-aa98107a1f03',
            'redirectLink' => 'http://example.com',
            'status' => 'received',
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('send')
            ->willReturn(new Response(200, [], json_encode(compact('data'))));

        $preApp = new PreAppResource($this->createTransport($client), static::BASE_URI);
        $response = $preApp->preApp(PreAppMessage::createFromArray(PreAppMessageTest::REQUIRED_DATA));

        $this->assertEquals(PreAppResponse::createFromArray($data), $response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testPreAppWithValidationError()
    {
        $data = [
            'error' => [
                'code' => 1,
                'details' => [
                    [
                        '@type' => 'type.googleapis.com/errdetails.v1.ValidationErrorDetails',
                        'error' => 'Validation failed on \'required\' tag',
                        'field' => 'PartnerData',
                    ],
                    [
                        '@type' => 'type.googleapis.com/errdetails.v1.ValidationErrorDetails',
                        'error' => 'Validation failed on \'required\' tag',
                        'field' => 'BillNumber',
                    ],
                    [
                        '@type' => 'type.googleapis.com/errdetails.v1.ValidationErrorDetails',
                        'error' => 'Validation failed on \'required\' tag',
                        'field' => 'BillAmount',
                    ],
                ],
                'message' => 'Validation fails',
                'prefix' => 'VL',
            ],
        ];

        $client = $this->createStub(ClientInterface::class);
        $client->method('send')
            ->willReturn(new Response(400, ['Content-Type' => 'application/json'], json_encode($data)));

        $preApp = new PreAppResource($this->createTransport($client), static::BASE_URI);

        try {
            $preApp->preApp(PreAppMessage::createFromArray(PreAppMessageTest::REQUIRED_DATA));
        } catch (ValidationException $e) {
            $this->assertEquals($data['error']['code'], $e->getCode());
            $this->assertEquals($data['error']['message'], $e->getMessage());
            $this->assertEquals(ValidationErrorResponse::createFromArray($data['error']), $e->getResponse());
        }
    }

    /**
     * @return void
     */
    protected function callResourceMethod(ClientInterface $client)
    {
        $resource = new PreAppResource($this->createTransport($client), static::BASE_URI);
        $resource->preApp(PreAppMessage::createFromArray(PreAppMessageTest::REQUIRED_DATA));
    }
}

