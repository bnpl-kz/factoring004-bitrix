<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * Represents error response.
 *
 * @psalm-immutable
 */
class ErrorResponse implements JsonSerializable, ArrayInterface
{
    protected string $code;
    protected string $message;
    protected ?string $description;
    protected ?string $type;
    protected ?string $error;

    public function __construct(
        string $code,
        string $message,
        ?string $description = null,
        ?string $type = null,
        ?string $error = null
    ) {
        $this->code = $code;
        $this->message = $message;
        $this->description = $description;
        $this->type = $type;
        $this->error = $error;
    }

    /**
     * @param array<string, mixed> $response
     * @psalm-param array{code: string|int, message: string, description?: string, type?: string, error?: string} $response
     */
    public static function createFromArray(array $response): ErrorResponse
    {
        return new self(
            (string) $response['code'],
            $response['message'],
            $response['description'] ?? null,
            $response['type'] ?? null,
            $response['error'] ?? null,
        );
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return array<string, mixed>
     * @psalm-return array{code: string, message: string, description?: string, type?: string, error?: string}
     */
    public function toArray(): array
    {
        $data = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];

        if ($this->getType()) {
            $data['type'] = $this->getType();
        }

        if ($this->getDescription()) {
            $data['description'] = $this->getDescription();
        }

        if ($this->getError()) {
            $data['error'] = $this->getError();
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
