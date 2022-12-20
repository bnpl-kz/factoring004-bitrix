<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\AbstractResource;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Response\ErrorResponse;
use BnplPartners\Factoring004\Transport\ResponseInterface;

class OtpResource extends AbstractResource
{
    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return \BnplPartners\Factoring004\Otp\DtoOtp
     */
    public function checkOtp(CheckOtp $otp)
    {
        $response = $this->postRequest('/accounting/checkOtp', $otp->toArray());

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return DtoOtp::createFromArray($response->getBody());
        }

        $this->handleClientError($response);

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return \BnplPartners\Factoring004\Otp\DtoOtp
     */
    public function sendOtp(SendOtp $otp)
    {
        $response = $this->postRequest('/accounting/sendOtp', $otp->toArray());

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return DtoOtp::createFromArray($response->getBody());
        }

        $this->handleClientError($response);

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return \BnplPartners\Factoring004\Otp\DtoOtp
     */
    public function checkOtpReturn(CheckOtpReturn $otp)
    {
        $response = $this->postRequest('/accounting/checkOtpReturn', $otp->toArray());

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return DtoOtp::createFromArray($response->getBody());
        }

        $this->handleClientError($response);

        throw new EndpointUnavailableException($response);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @return \BnplPartners\Factoring004\Otp\DtoOtp
     */
    public function sendOtpReturn(SendOtpReturn $otp)
    {
        $response = $this->postRequest('/accounting/sendOtpReturn', $otp->toArray());

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return DtoOtp::createFromArray($response->getBody());
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

            if (isset($data['error']) && is_array($data['error'])) {
                $data = $data['error'];
            }

            if (isset($data['fault']) && is_array($data['fault'])) {
                $data = $data['fault'];
            }

            if (empty($data['code'])) {
                throw new UnexpectedResponseException($response, isset($data['message']) ? $data['message'] : 'Unexpected response schema');
            }

            $code = (int) $data['code'];

            if (in_array($code, static::AUTH_ERROR_CODES, true)) {
                throw new AuthenticationException(isset($data['description']) ? $data['description'] : '', isset($data['message']) ? $data['message'] : '', $code);
            }

            /** @psalm-suppress ArgumentTypeCoercion */
            throw new ErrorResponseException(ErrorResponse::createFromArray($data));
        }
    }
}
