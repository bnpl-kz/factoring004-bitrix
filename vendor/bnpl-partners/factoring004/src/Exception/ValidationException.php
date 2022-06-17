<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Exception;

use BnplPartners\Factoring004\Response\ValidationErrorResponse;
use Throwable;

class ValidationException extends ApiException
{
    /**
     * @var \BnplPartners\Factoring004\Response\ValidationErrorResponse
     */
    protected $response;

    public function __construct(ValidationErrorResponse $response, Throwable $previous = null)
    {
        parent::__construct($response->getMessage(), $response->getCode(), $previous);

        $this->response = $response;
    }

    public function getResponse(): ValidationErrorResponse
    {
        return $this->response;
    }
}
