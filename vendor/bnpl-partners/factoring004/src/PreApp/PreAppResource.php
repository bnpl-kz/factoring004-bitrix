<?php

declare(strict_types=1);

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
    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\ValidationException
     * @throws \BnplPartners\Factoring004\Exception\ApiException
     */
    public function preApp(PreAppMessage $data): PreAppResponse
    {
        $response = $this->postRequest('/bnpl/v3/preapp', $data->toArray());

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
     */
    private function handleClientError(ResponseInterface $response): void
    {
        $data = $response->getBody();

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
            throw new UnexpectedResponseException($response, $data['message'] ?? 'Unexpected response schema');
        }

        if ($response->getStatusCode() === 401) {
            throw new AuthenticationException('', $data['message'] ?? '', $data['code']);
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        throw new ErrorResponseException(ErrorResponse::createFromArray($data));
    }
}
