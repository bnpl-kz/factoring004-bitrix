<?php

namespace BnplPartners\Factoring004\Transport;

/**
 * Simple abstraction layer over PSR-7 response.
 *
 * @template T
 */
interface ResponseInterface
{
    /**
     * @return int
     */
    public function getStatusCode();

    /**
     * @return array<string, string>
     */
    public function getHeaders();

    /**
     * @return array<array-key, T>
     */
    public function getBody();
}
