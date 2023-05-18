<?php

namespace BnplPartners\Factoring004\ChangeStatus;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class SuccessResponse implements JsonSerializable, ArrayInterface
{
    /**
     * @var string
     */
    private $error;
    /**
     * @var string
     */
    private $msg;
    /**
     * @var string
     */
    private $merchantOrderId;

    /**
     * @param string $error
     * @param string $msg
     * @param string $merchantOrderId
     */
    public function __construct($error, $msg, $merchantOrderId = '')
    {
        $this->error = $error;
        $this->msg = $msg;
        $this->merchantOrderId = $merchantOrderId;
    }

    /**
     * @param array<string, string> $response
     * @psalm-param array{error: string, msg: string, merchantOrderId?: string} $response
     *
     * @return \BnplPartners\Factoring004\ChangeStatus\SuccessResponse
     */
    public static function createFromArray(array $response)
    {
        return new self($response['error'], $response['msg'], isset($response['merchantOrderId']) ? $response['merchantOrderId'] : '');
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
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return string
     */
    public function getMerchantOrderId()
    {
        return $this->merchantOrderId;
    }

    /**
     * @psalm-return array{error: string, msg: string, merchantOrderId: string}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'error' => $this->getError(),
            'msg' => $this->getMsg(),
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
