<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Exception\ApiException;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\PackageException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\PreApp\PreAppMessage;
use BnplPartners\Factoring004\PreApp\PreAppMessageTest;
use BnplPartners\Factoring004\PreApp\PreAppResource;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Response\PreAppResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;
use InvalidArgumentException;
use BnplPartners\Factoring004\AbstractTestCase;

class OrderManagerPreAppTest extends AbstractTestCase
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testPreAppWithArray()
    {
        $message = PreAppMessage::createFromArray(PreAppMessageTest::REQUIRED_DATA);
        $preAppResponse = $this->createStub(PreAppResponse::class);

        $preAppResource = $this->createMock(PreAppResource::class);
        $preAppResource->expects($this->once())
            ->method('preApp')
            ->with($message)
            ->willReturn($preAppResponse);

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('preApps')
            ->willReturn($preAppResource);

        $manager = new OrderManager($api);
        $this->assertEquals($preAppResponse, $manager->preApp(PreAppMessageTest::REQUIRED_DATA));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testPreAppWithMessage()
    {
        $message = PreAppMessage::createFromArray(PreAppMessageTest::REQUIRED_DATA);
        $preAppResponse = $this->createStub(PreAppResponse::class);

        $preAppResource = $this->createMock(PreAppResource::class);
        $preAppResource->expects($this->once())
            ->method('preApp')
            ->with($message)
            ->willReturn($preAppResponse);

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('preApps')
            ->willReturn($preAppResource);

        $manager = new OrderManager($api);
        $this->assertEquals($preAppResponse, $manager->preApp($message));
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     * @return void
     */
    public function testPreAppWithInvalidArgument()
    {
        $api = $this->createMock(Api::class);
        $api->expects($this->never())
            ->method('__get')
            ->with('preApps');

        $manager = new OrderManager($api);
        $this->expectException(InvalidArgumentException::class);

        $manager->preApp(true);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\PackageException
     *
     * @dataProvider exceptionsProvider
     * @return void
     */
    public function testPreAppWithException(PackageException $exception)
    {
        $preAppResource = $this->createMock(PreAppResource::class);
        $preAppResource->expects($this->once())
            ->method('preApp')
            ->willThrowException($exception);

        $api = $this->createMock(Api::class);
        $api->expects($this->once())
            ->method('__get')
            ->with('preApps')
            ->willReturn($preAppResource);

        $manager = new OrderManager($api);
        $this->expectException(get_class($exception));

        $manager->preApp(PreAppMessageTest::REQUIRED_DATA);
    }

    /**
     * @return mixed[]
     */
    public function exceptionsProvider()
    {
        return [
            [new NetworkException()],
            [new DataSerializationException()],
            [new TransportException()],
            [new UnexpectedResponseException($this->createStub(ResponseInterface::class))],
            [new EndpointUnavailableException($this->createStub(ResponseInterface::class))],
            [new ErrorResponseException(new ErrorResponse('1', 'test'))],
            [new AuthenticationException('Invalid Credentials')],
            [new ApiException()],
            [new PackageException()],
        ];
    }
}

