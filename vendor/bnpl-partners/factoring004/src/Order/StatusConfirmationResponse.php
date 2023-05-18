<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class StatusConfirmationResponse implements ArrayInterface, JsonSerializable
{
    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public static function create($message)
    {
        return new self($message);
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{message: string}
     */
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
