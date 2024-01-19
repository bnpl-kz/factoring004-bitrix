<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\GetStatus;

class StatusResponse
{
    private string $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * @param array<string, string> $response
     * @psalm-param array{status: string} $response
     * @return StatusResponse
     */
    public static function create(array $response): StatusResponse
    {
        return new self($response['status'] ?? "");
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}