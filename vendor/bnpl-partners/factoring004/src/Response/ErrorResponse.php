<?php

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
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var string|null
     */
    protected $description;
    /**
     * @var string|null
     */
    protected $type;
    /**
     * @var string|null
     */
    protected $error;

    /**
     * @param string $code
     * @param string $message
     * @param string $description
     * @param string $type
     * @param string $error
     */
    public function __construct(
        $code,
        $message,
        $description = null,
        $type = null,
        $error = null
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
     * @return \BnplPartners\Factoring004\Response\ErrorResponse
     */
    public static function createFromArray(array $response)
    {
        return new self((string) $response['code'], $response['message'], isset($response['description']) ? $response['description'] : null, isset($response['type']) ? $response['type'] : null, isset($response['error']) ? $response['error'] : null);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return array<string, mixed>
     * @psalm-return array{code: string, message: string, description?: string, type?: string, error?: string}
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
