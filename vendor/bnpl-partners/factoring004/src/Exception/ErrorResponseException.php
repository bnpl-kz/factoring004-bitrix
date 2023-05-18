<?php

namespace BnplPartners\Factoring004\Exception;

use BnplPartners\Factoring004\Response\ErrorResponse;
use Throwable;

class ErrorResponseException extends ApiException
{
    /**
     * @var \BnplPartners\Factoring004\Response\ErrorResponse
     */
    private $errorResponse;

    /**
     * @param \Throwable $previous
     */
    public function __construct(ErrorResponse $errorResponse, $previous = null)
    {
        parent::__construct(
            $errorResponse->getMessage(),
            (int) $errorResponse->getCode(),
            $previous
        );

        $this->errorResponse = $errorResponse;
    }

    /**
     * @return \BnplPartners\Factoring004\Response\ErrorResponse
     */
    public function getErrorResponse()
    {
        return $this->errorResponse;
    }
}
