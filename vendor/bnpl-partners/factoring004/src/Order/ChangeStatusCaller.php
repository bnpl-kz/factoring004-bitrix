<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ChangeStatus\AbstractMerchantOrder;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;

class ChangeStatusCaller
{
    private ChangeStatusResource $resource;
    private string $merchantId;

    public function __construct(ChangeStatusResource $resource, string $merchantId)
    {
        $this->resource = $resource;
        $this->merchantId = $merchantId;
    }

    public static function create(ChangeStatusResource $resource, string $merchantId): ChangeStatusCaller
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
     */
    public function call(AbstractMerchantOrder $order): SuccessResponse
    {
        $response = $this->resource->changeStatusJson([
            new MerchantsOrders($this->merchantId, [$order]),
        ]);

        if ($response->getSuccessfulResponses()) {
            return $response->getSuccessfulResponses()[0];
        }

        throw new ErrorResponseException(
            new ErrorResponse(
                $response->getErrorResponses()[0]->getCode(),
                $response->getErrorResponses()[0]->getMessage(),
                null,
                null,
                $response->getErrorResponses()[0]->getError(),
            )
        );
    }
}
