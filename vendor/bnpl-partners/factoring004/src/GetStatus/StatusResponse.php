<?php

namespace BnplPartners\Factoring004\GetStatus;

class StatusResponse
{
    /**
     * @var string
     */
    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @param array<string, string> $response
     * @psalm-param array{status: string} $response
     * @return StatusResponse
     */
    public static function create(array $response)
    {
        return new self(isset($response['status']) ? $response['status'] : '');
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}