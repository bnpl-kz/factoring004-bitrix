<?php

declare(strict_types=1);

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

    public function __construct(string $msg, bool $error = false)
    {
        $this->msg = $msg;
        $this->error = $error;
    }

    /**
     * @param array<string, string> $changeStatus
     * @psalm-param array{msg: string, error?: bool|string} $changeStatus
     */
    public static function createFromArray($changeStatus): DtoOtp
    {
        $error = $changeStatus['error'] ?? false;

        if (is_string($error)) {
            $error = !($error === 'false');
        }

        return new self($changeStatus['msg'], $error);
    }

    public function getMsg(): string
    {
        return $this->msg;
    }

    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @psalm-return array{msg: string, error: bool}
     */
    public function toArray(): array
    {
        return [
            'msg' => $this->getMsg(),
            'error' => $this->isError(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
