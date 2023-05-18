<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractResource;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Response\PreAppResponse;
use BnplPartners\Factoring004\Response\ValidationErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;

class PreAppResource extends AbstractResource
{
    private $preappPath = '/bnpl/v3/preapp';

    /**
     * @param string $preappPath
     * @return PreAppResource
     */
    public function setPreappPath($preappPath)
    {
        $this->preappPath = $preappPath;
        return $this;
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\ValidationException
     * @throws \BnplPartners\Factoring004\Exception\ApiException
     * @return \BnplPartners\Factoring004\Response\PreAppResponse
     */
    public function preApp(PreAppMessage $data)
    {
        $response = $this->postRequest($this->preappPath, $data->toArray());

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return PreAppResponse::createFromArray($response->getBody()['data']);
        }

        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            $this->handleClientError($response);
        }

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\ValidationException
     * @return void
     */
    private function handleClientError(ResponseInterface $response)
    {
        $data = $response->getBody();

        if ($response->getStatusCode() === 401) {
            throw new AuthenticationException('', isset($data['message']) ? $data['message'] : '', $data['code']);
        }

        if (isset($data['error'])) {
            $data = $data['error'];

            if (isset($data['details'])) {
                throw new ValidationException(ValidationErrorResponse::createFromArray($data));
            }
        }

        if (isset($data['fault'])) {
            $data = $data['fault'];
        }

        if (empty($data['code'])) {
            throw new UnexpectedResponseException($response, isset($data['message']) ? $data['message'] : 'Unexpected response schema');
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        throw new ErrorResponseException(ErrorResponse::createFromArray($data));
    }
}
