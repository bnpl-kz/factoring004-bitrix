<?php

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

    /**
     * @param string $secretKey
     */
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * @param string $secretKey
     * @return \BnplPartners\Factoring004\Signature\PostLinkSignatureCalculator
     */
    public static function create($secretKey)
    {
        return new self($secretKey);
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     * @return string
     */
    public function calculate(array $data)
    {
        return $this->calculateHash($this->convertDataToString($data));
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     * @return string
     */
    private function convertDataToString(array $data)
    {
        $str = '';

        foreach (static::FIELDS as $field) {
            if (isset($data[$field])) {
                $str .= $field . static::FIELD_SEPARATOR . $data[$field] . static::FIELD_SEPARATOR;
            }
        }

        return substr($str, 0, -1);
    }

    /**
     * @param string $data
     * @return string
     */
    private function calculateHash($data)
    {
        return hash_hmac(static::HASH_ALGO, $data, $this->secretKey);
    }
}
