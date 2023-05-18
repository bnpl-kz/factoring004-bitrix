<?php

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

    /**
     * @param string $code
     * @param string $error
     * @param string $message
     * @param string $merchantOrderId
     */
    public function __construct($code, $error, $message, $merchantOrderId = '')
    {
        $this->code = $code;
        $this->error = $error;
        $this->message = $message;
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @param array<string, string> $response
     * @psalm-param array{code: string, error: string, message: string, merchantOrderId?: string} $response
     * @return \BnplPartners\Factoring004\ChangeStatus\ErrorResponse
     */
    public static function createFromArray(array $response)
    {
        return new self($response['code'], $response['error'], $response['message'], isset($response['merchantOrderId']) ? $response['merchantOrderId'] : '');
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
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @psalm-return array{code: string, error: string, message: string, merchantOrderId: string}
     * @return mixed[]
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
