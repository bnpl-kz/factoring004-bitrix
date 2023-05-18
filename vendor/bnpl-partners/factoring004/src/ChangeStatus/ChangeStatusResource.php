<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\AbstractResource;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Transport\ResponseInterface;

class ChangeStatusResource extends AbstractResource
{
    private $changeStatusPath = '/accounting/v1/changeStatus/json';

    /**
     * @param string $changeStatusPath
     * @return ChangeStatusResource
     */
    public function setChangeStatusPath($changeStatusPath)
    {
        $this->changeStatusPath = $changeStatusPath;
        return $this;
    }

    /**
     * @param \BnplPartners\Factoring004\ChangeStatus\MerchantsOrders[] $merchantOrders
     *
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return \BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse
     */
    public function changeStatusJson(array $merchantOrders)
    {
        $response = $this->request(
            'PUT',
            $this->changeStatusPath,
            array_map(function (MerchantsOrders $orders) {
                return $orders->toArray();
            }, $merchantOrders)
        );

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return ChangeStatusResponse::createFromArray($response->getBody());
        }

        $this->handleClientError($response);

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return void
     */
    private function handleClientError(ResponseInterface $response)
    {
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            $data = $response->getBody();

            if ($response->getStatusCode() === 401) {
                throw new AuthenticationException('', isset($data['message']) ? $data['message'] : '', $data['code']);
            }

            if (isset($data['error']) && is_array($data['error'])) {
                $data = $data['error'];
            }

            if (isset($data['fault']) && is_array($data['fault'])) {
                $data = $data['fault'];
            }

            if (empty($data['code'])) {
                throw new UnexpectedResponseException($response, isset($data['message']) ? $data['message'] : 'Unexpected response schema');
            }

            /** @psalm-suppress ArgumentTypeCoercion */
            throw new ErrorResponseException(\BnplPartners\Factoring004\Response\ErrorResponse::createFromArray($data));
        }
    }
}
