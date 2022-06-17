<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\ArrayInterface;
use BnplPartners\Factoring004\PreApp\ValidationErrorDetail;
use JsonSerializable;

/**
 * Represents validation error response.
 *
 * @psalm-immutable
 */
class ValidationErrorResponse implements JsonSerializable, ArrayInterface
{
    /**
     * @var int
     */
    private $code;

    /**
     * @var \BnplPartners\Factoring004\PreApp\ValidationErrorDetail[]
     */
    private $details;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @param \BnplPartners\Factoring004\PreApp\ValidationErrorDetail[] $details
     * @param string|null $prefix
     */
    public function __construct(int $code, array $details, string $message, $prefix = null)
    {
        $this->code = $code;
        $this->details = $details;
        $this->message = $message;
        $this->prefix = $prefix;
    }

    /**
     * @param array<string, mixed> $response
     *
     * @psalm-param array{
          * code: int|string,
          * details: array{error: string, field: string}[],
          * message: string,
          * prefix?: string
       * } $error
     */
    public static function createFromArray($response): ValidationErrorResponse
    {
        return new self((int) $response['code'], ValidationErrorDetail::createMany($response['details'] ?? []), $response['message'], $response['prefix'] ?? null);
    }

    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\ValidationErrorDetail[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return array<string, mixed>
     * @psalm-return array{code: int, details: array{error: string, field: string}[], message: string, prefix?: string}
     */
    public function toArray(): array
    {
        $data = [
            'code' => $this->getCode(),
            'details' => array_map(function ($detail) {
                return $detail->toArray();
            }, $this->getDetails()),
            'message' => $this->getMessage(),
        ];

        if ($this->getPrefix()) {
            $data['prefix'] = $this->getPrefix();
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
