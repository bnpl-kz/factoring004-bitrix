<?php

namespace BnplPartners\Factoring004\Transport;

use BnplPartners\Factoring004\Exception\DataSerializationException;
use Psr\Http\Message\ResponseInterface as PsrResponse;

/**
 * @template T
 * @implements ResponseInterface<T>
 */
class Response implements ResponseInterface
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var array<string, string>
     */
    private $headers;

    /**
     * @var array<array-key, T>
     */
    private $body;

    /**
     * @param array<string, string> $headers
     * @param array<array-key, T> $body
     * @param int $statusCode
     */
    public function __construct($statusCode, array $headers, array $body = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @return \BnplPartners\Factoring004\Transport\Response
     */
    public static function createFromPsrResponse(PsrResponse $response)
    {
        $content = (string) $response->getBody();
        $data = [];

        if ($content) {
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new DataSerializationException('Response has invalid JSON');
            }
        }

        return new self($response->getStatusCode(), array_map(function (array $values) {
            return implode(', ', $values);
        }, $response->getHeaders()), $data);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed[]
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed[]
     */
    public function getBody()
    {
        return $this->body;
    }
}
