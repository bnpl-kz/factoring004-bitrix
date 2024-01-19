<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\GetStatus;

use BnplPartners\Factoring004\AbstractResource;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\DataSerializationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;

class BillResource extends AbstractResource
{
    /**
     * @throws ErrorResponseException
     * @throws NetworkException
     * @throws DataSerializationException
     * @throws UnexpectedResponseException
     * @throws EndpointUnavailableException
     * @throws AuthenticationException
     * @throws TransportException
     */
    public function getStatus(string $orderID): StatusResponse
    {
        $response = $this->request('GET', sprintf('/bnpl/bill/%s/status', $orderID));

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return StatusResponse::create($response->getBody());
        }

        $this->handleClientError($response);

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws AuthenticationException
     * @throws ErrorResponseException
     * @throws UnexpectedResponseException
     */
    private function handleClientError(ResponseInterface $response): void
    {
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            $data = $response->getBody();

            if (isset($data['error']) && is_array($data['error'])) {
                $data = $data['error'];
            }

            if (isset($data['fault']) && is_array($data['fault'])) {
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

}