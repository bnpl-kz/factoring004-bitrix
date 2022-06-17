<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Exception;

use BnplPartners\Factoring004\Transport\ResponseInterface;
use Throwable;

class UnexpectedResponseException extends ApiException
{
    /**
     * @var \BnplPartners\Factoring004\Transport\ResponseInterface
     */
    private $response;

    public function __construct(
        ResponseInterface $response,
        string $message = '',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
