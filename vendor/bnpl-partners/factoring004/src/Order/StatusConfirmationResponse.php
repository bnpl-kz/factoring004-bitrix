<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class StatusConfirmationResponse implements ArrayInterface, JsonSerializable
{
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public static function create(string $message): StatusConfirmationResponse
    {
        return new self($message);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{message: string}
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
