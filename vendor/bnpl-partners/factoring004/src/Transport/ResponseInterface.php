<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Transport;

/**
 * Simple abstraction layer over PSR-7 response.
 *
 * @template T
 */
interface ResponseInterface
{
    public function getStatusCode(): int;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;

    /**
     * @return array<array-key, T>
     */
    public function getBody(): array;
}
