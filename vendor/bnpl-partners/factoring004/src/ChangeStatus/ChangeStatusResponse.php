<?php

declare(strict_types=1);

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
    private array $successfulResponses;

    /**
     * @var \BnplPartners\Factoring004\ChangeStatus\ErrorResponse[]
     */
    private array $errorResponses;

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
    public static function createFromArray(array $responses): ChangeStatusResponse
    {
        return new self(
            array_map(
                fn(array $response) => SuccessResponse::createFromArray($response),
                $responses['successfulResponses'] ?? $responses['SuccessfulResponses'] ?? []
            ),
            array_map(
                fn(array $response) => ErrorResponse::createFromArray($response),
                $responses['errorResponses'] ?? $responses['ErrorResponses'] ?? []
            ),
        );
    }

    public function getSuccessfulResponses(): array
    {
        return $this->successfulResponses;
    }

    public function getErrorResponses(): array
    {
        return $this->errorResponses;
    }

    /**
     * @psalm-return array{
         SuccessfulResponses: array{error: string, msg: string}[],
         ErrorResponses: array{code: string, error: string, message: string}[],
     }
     */
    public function toArray(): array
    {
        return [
            'SuccessfulResponses' => array_map(
                fn(SuccessResponse $response) => $response->toArray(),
                $this->getSuccessfulResponses(),
            ),
            'ErrorResponses' => array_map(
                fn(ErrorResponse $response) => $response->toArray(),
                $this->getErrorResponses(),
            ),
        ];
    }

    /**
     * @return array<string, array<string, mixed>[]>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
