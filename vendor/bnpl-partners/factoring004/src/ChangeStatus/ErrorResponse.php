<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class ErrorResponse implements JsonSerializable, ArrayInterface
{
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $error;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $merchantOrderId;

    public function __construct(string $code, string $error, string $message, string $merchantOrderId = '')
    {
        $this->code = $code;
        $this->error = $error;
        $this->message = $message;
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @param array<string, string> $response
     * @psalm-param array{code: string, error: string, message: string, merchantOrderId?: string} $response
     */
    public static function createFromArray($response): ErrorResponse
    {
        return new self($response['code'], $response['error'], $response['message'], $response['merchantOrderId'] ?? '');
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getMerchantOrderId(): string
    {
        return $this->merchantOrderId;
    }

    /**
     * @psalm-return array{code: string, error: string, message: string, merchantOrderId: string}
     */
    public function toArray(): array
    {
        return [
            'code' => $this->getCode(),
            'error' => $this->getError(),
            'message' => $this->getMessage(),
            'merchantOrderId' => $this->getMerchantOrderId(),
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
