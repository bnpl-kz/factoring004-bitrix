<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\DataSerializationException;
use Psr\Http\Message\ResponseInterface as PsrResponse;

/**
 * @template T
 * @implements ResponseInterface<T>
 */
class Response implements ResponseInterface
{
    private int $statusCode;

    /**
     * @var array<string, string>
     */
    private array $headers;

    /**
     * @var array<array-key, T>
     */
    private array $body;

    /**
     * @param array<string, string> $headers
     * @param array<array-key, T> $body
     */
    public function __construct(int $statusCode, array $headers, array $body = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     */
    public static function createFromPsrResponse(PsrResponse $response): Response
    {
        $content = (string) $response->getBody();
        $data = [];

        if ($content) {
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DataSerializationException('Response has invalid JSON');
            }
        }

        return new self(
            $response->getStatusCode(),
            array_map(fn(array $values) => implode(', ', $values), $response->getHeaders()),
            $data,
        );
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): array
    {
        return $this->body;
    }
}
