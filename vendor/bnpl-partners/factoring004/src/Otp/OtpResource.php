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
    private $checkOtpPath = '/accounting/v1/checkOtp';
    private $sendOtpPath = '/accounting/v1/sendOtp';
    private $checkOtpReturnPath = '/accounting/v1/checkOtpReturn';
    private $sendOtpReturnPath = '/accounting/v1/sendOtpReturn';

    /**
     * @param string $checkOtpPath
     * @return OtpResource
     */
    public function setCheckOtpPath($checkOtpPath)
    {
        $this->checkOtpPath = $checkOtpPath;
        return $this;
    }

    /**
     * @param string $sendOtpPath
     * @return OtpResource
     */
    public function setSendOtpPath($sendOtpPath)
    {
        $this->sendOtpPath = $sendOtpPath;
        return $this;
    }

    /**
     * @param string $checkOtpReturnPath
     * @return OtpResource
     */
    public function setCheckOtpReturnPath($checkOtpReturnPath)
    {
        $this->checkOtpReturnPath = $checkOtpReturnPath;
        return $this;
    }

    /**
     * @param string $sendOtpReturnPath
     * @return OtpResource
     */
    public function setSendOtpReturnPath($sendOtpReturnPath)
    {
        $this->sendOtpReturnPath = $sendOtpReturnPath;
        return  $this;
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
    public function checkOtp(CheckOtp $otp)
    {
        $response = $this->postRequest($this->checkOtpPath, $otp->toArray());

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
        $response = $this->postRequest($this->sendOtpPath, $otp->toArray());

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
        $response = $this->postRequest($this->checkOtpReturnPath, $otp->toArray());

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
        $response = $this->postRequest($this->sendOtpReturnPath, $otp->toArray());

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
            throw new ErrorResponseException(ErrorResponse::createFromArray($data));
        }
    }
}
