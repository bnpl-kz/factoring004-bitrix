<?php

declare(strict_types=1);

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

    public function __construct(string $error, string $msg, string $merchantOrderId = '')
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
    public static function createFromArray($response): SuccessResponse
    {
        return new self($response['error'], $response['msg'], $response['merchantOrderId'] ?? '');
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getMsg(): string
    {
        return $this->msg;
    }

    public function getMerchantOrderId(): string
    {
        return $this->merchantOrderId;
    }

    /**
     * @psalm-return array{error: string, msg: string, merchantOrderId: string}
     */
    public function toArray(): array
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
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
