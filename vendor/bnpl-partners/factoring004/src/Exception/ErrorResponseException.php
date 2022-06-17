<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Exception;

use BnplPartners\Factoring004\Response\ErrorResponse;
use Throwable;

class ErrorResponseException extends ApiException
{
    /**
     * @var \BnplPartners\Factoring004\Response\ErrorResponse
     */
    private $errorResponse;

    public function __construct(ErrorResponse $errorResponse, Throwable $previous = null)
    {
        parent::__construct(
            $errorResponse->getMessage(),
            (int) $errorResponse->getCode(),
            $previous
        );

        $this->errorResponse = $errorResponse;
    }

    public function getErrorResponse(): ErrorResponse
    {
        return $this->errorResponse;
    }
}
