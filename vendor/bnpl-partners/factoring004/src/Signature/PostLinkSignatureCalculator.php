<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Signature;

/**
 * @psalm-immutable
 */
class PostLinkSignatureCalculator
{
    const FIELD_SEPARATOR = ':';
    const HASH_ALGO = 'sha512';
    const FIELDS = ['status', 'preappId', 'billNumber'];

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public static function create(string $secretKey): PostLinkSignatureCalculator
    {
        return new self($secretKey);
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     */
    public function calculate(array $data): string
    {
        return $this->calculateHash($this->convertDataToString($data));
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     */
    private function convertDataToString(array $data): string
    {
        $str = '';

        foreach (static::FIELDS as $field) {
            if (isset($data[$field])) {
                $str .= $field . static::FIELD_SEPARATOR . $data[$field] . static::FIELD_SEPARATOR;
            }
        }

        return substr($str, 0, -1);
    }

    private function calculateHash(string $data): string
    {
        return hash_hmac(static::HASH_ALGO, $data, $this->secretKey);
    }
}
