<?php

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

    /**
     * @param string $secretKey
     * @param \BnplPartners\Factoring004\Signature\PostLinkSignatureCalculator|null $calculator
     */
    public function __construct($secretKey, PostLinkSignatureCalculator $calculator = null)
    {
        $this->calculator = isset($calculator) ? $calculator : PostLinkSignatureCalculator::create($secretKey);
    }

    /**
     * @param string $secretKey
     * @return \BnplPartners\Factoring004\Signature\PostLinkSignatureValidator
     * @param \BnplPartners\Factoring004\Signature\PostLinkSignatureCalculator|null $calculator
     */
    public static function create(
        $secretKey,
        PostLinkSignatureCalculator $calculator = null
    ) {
        return new self($secretKey, $calculator);
    }

    /**
     * Checks signature of incoming post link data.
     *
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, billNumber: string, preappId: string, scoring?: int} $data
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     * @return void
     * @param string $knownHash
     */
    public function validate(array $data, $knownHash)
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
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     * @return void
     * @param string $signatureKeyName
     */
    public function validateData(array $data, $signatureKeyName = 'signature')
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
