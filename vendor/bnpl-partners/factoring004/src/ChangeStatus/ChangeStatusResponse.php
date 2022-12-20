<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class ChangeStatusResponse implements JsonSerializable, ArrayInterface
{
    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\SuccessResponse[]
     */
    private $successfulResponses;

    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\ErrorResponse[]
     */
    private $errorResponses;

    /**
     * @param \BnplPartners\Factoring004\ChangeStatus\SuccessResponse[] $successfulResponses
     * @param \BnplPartners\Factoring004\ChangeStatus\ErrorResponse[] $errorResponses
     */
    public function __construct(array $successfulResponses, array $errorResponses)
    {
        $this->successfulResponses = $successfulResponses;
        $this->errorResponses = $errorResponses;
    }

    /**
     * @param array<string, array<string, mixed>[]> $responses
     * @psalm-param array{
         SuccessfulResponses?: array{error: string, msg: string}[],
         successfulResponses?: array{error: string, msg: string}[],
         ErrorResponses?: array{code: string, error: string, message: string}[],
         errorResponses?: array{code: string, error: string, message: string}[],
     } $responses
     *
     * @return \BnplPartners\Factoring004\ChangeStatus\ChangeStatusResponse
     */
    public static function createFromArray(array $responses)
    {
        return new self(array_map(
            function (array $response) {
                return SuccessResponse::createFromArray($response);
            },
            isset($responses['successfulResponses']) ? $responses['successfulResponses'] : (isset($responses['SuccessfulResponses']) ? $responses['SuccessfulResponses'] : [])
        ), array_map(
            function (array $response) {
                return ErrorResponse::createFromArray($response);
            },
            isset($responses['errorResponses']) ? $responses['errorResponses'] : (isset($responses['ErrorResponses']) ? $responses['ErrorResponses'] : [])
        ));
    }

    /**
     * @return mixed[]
     */
    public function getSuccessfulResponses()
    {
        return $this->successfulResponses;
    }

    /**
     * @return mixed[]
     */
    public function getErrorResponses()
    {
        return $this->errorResponses;
    }

    /**
    * @psalm-return array{
        SuccessfulResponses: array{error: string, msg: string}[],
        ErrorResponses: array{code: string, error: string, message: string}[],
    }
     * @return mixed[]
    */
    public function toArray()
    {
        return [
            'SuccessfulResponses' => array_map(function (SuccessResponse $response) {
                return $response->toArray();
            }, $this->getSuccessfulResponses()),
            'ErrorResponses' => array_map(function (ErrorResponse $response) {
                return $response->toArray();
            }, $this->getErrorResponses()),
        ];
    }

    /**
     * @return array<string, array<string, mixed>[]>
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
