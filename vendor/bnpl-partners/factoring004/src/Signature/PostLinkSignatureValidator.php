<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Signature;

use BnplPartners\Factoring004\Exception\InvalidSignatureException;

/**
 * @psalm-immutable
 */
class PostLinkSignatureValidator
{
    /**
     * @var \BnplPartners\Factoring004\Signature\PostLinkSignatureCalculator
     */
    private $calculator;

    public function __construct(string $secretKey, PostLinkSignatureCalculator $calculator = null)
    {
        $this->calculator = $calculator ?? PostLinkSignatureCalculator::create($secretKey);
    }

    public static function create(
        string $secretKey,
        PostLinkSignatureCalculator $calculator = null
    ): PostLinkSignatureValidator {
        return new self($secretKey, $calculator);
    }

    /**
     * Checks signature of incoming post link data.
     *
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     *
     * @return void
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function validate(array $data, string $knownHash)
    {
        $hash = $this->calculator->calculate($data);

        if (!hash_equals($hash, $knownHash)) {
            throw new InvalidSignatureException(
                "Invalid signature. Known hash {$knownHash} is not equal to {$hash}"
            );
        }
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, signature?: string, scoring?: int} $data
     *
     * @return void
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function validateData(array $data, string $signatureKeyName = 'signature')
    {
        if (empty($data[$signatureKeyName])) {
            throw new InvalidSignatureException('Known signature not found');
        }

        $knownHash = (string) $data[$signatureKeyName];
        unset($data[$signatureKeyName]);

        /** @psalm-var array{status: string, billNumber: string, preappId: string, scoring?: int} $data */
        $this->validate($data, $knownHash);
    }
}
