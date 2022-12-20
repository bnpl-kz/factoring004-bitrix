<?php

namespace BnplPartners\Factoring004\Otp;

use BnplPartners\Factoring004\ArrayInterface;
use JsonSerializable;

class DtoOtp implements JsonSerializable, ArrayInterface
{
    /**
     * @var string
     */
    private $msg;
    /**
     * @var bool
     */
    private $error;

    /**
     * @param string $msg
     * @param bool $error
     */
    public function __construct($msg, $error = false)
    {
        $this->msg = $msg;
        $this->error = $error;
    }

    /**
     * @param array<string, string> $changeStatus
     * @psalm-param array{msg: string, error?: bool|string} $changeStatus
     * @return \BnplPartners\Factoring004\Otp\DtoOtp
     */
    public static function createFromArray(array $changeStatus)
    {
        $error = isset($changeStatus['error']) ? $changeStatus['error'] : false;

        if (is_string($error)) {
            $error = !($error === 'false');
        }

        return new self($changeStatus['msg'], $error);
    }

    /**
     * @return string
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * @psalm-return array{msg: string, error: bool}
     * @return mixed[]
     */
    public function toArray()
    {
        return [
            'msg' => $this->getMsg(),
            'error' => $this->isError(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
