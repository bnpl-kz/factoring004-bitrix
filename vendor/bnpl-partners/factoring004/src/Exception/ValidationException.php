<?php

namespace BnplPartners\Factoring004\Exception;

use BnplPartners\Factoring004\Response\ValidationErrorResponse;
use Throwable;

class ValidationException extends ApiException
{
    /**
     * @var \BnplPartners\Factoring004\Response\ValidationErrorResponse
     */
    protected $response;

    /**
     * @param \Throwable $previous
     */
    public function __construct(ValidationErrorResponse $response, $previous = null)
    {
        parent::__construct($response->getMessage(), $response->getCode(), $previous);

        $this->response = $response;
    }

    /**
     * @return \BnplPartners\Factoring004\Response\ValidationErrorResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
