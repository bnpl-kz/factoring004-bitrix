<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;

class ChangeStatusCaller
{
    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource
     */
    private $resource;
    /**
     * @var string
     */
    private $merchantId;

    /**
     * @param string $merchantId
     */
    public function __construct(ChangeStatusResource $resource, $merchantId)
    {
        $this->resource = $resource;
        $this->merchantId = $merchantId;
    }

    /**
     * @param string $merchantId
     * @return \BnplPartners\Factoring004\Order\ChangeStatusCaller
     */
    public static function create(ChangeStatusResource $resource, $merchantId)
    {
        return new self($resource, $merchantId);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @return \BnplPartners\Factoring004\ChangeStatus\SuccessResponse
     */
    public function call(AbstractMerchantOrder $order)
    {
        $response = $this->resource->changeStatusJson([
            new MerchantsOrders($this->merchantId, [$order]),
        ]);

        if ($response->getSuccessfulResponses()) {
            return $response->getSuccessfulResponses()[0];
        }

        throw new ErrorResponseException(
            new ErrorResponse($response->getErrorResponses()[0]->getCode(), $response->getErrorResponses()[0]->getMessage(), null, null, $response->getErrorResponses()[0]->getError())
        );
    }
}
