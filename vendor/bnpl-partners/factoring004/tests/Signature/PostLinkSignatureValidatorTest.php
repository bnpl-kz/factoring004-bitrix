<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Signature;

use BnplPartners\Factoring004\Exception\InvalidSignatureException;
use PHPUnit\Framework\TestCase;

class PostLinkSignatureValidatorTest extends TestCase
{
    public function testCreate(): void
    {
        $expected = new PostLinkSignatureValidator('test');
        $actual = PostLinkSignatureValidator::create('test');
        $this->assertEquals($expected, $actual);

        $expected = new PostLinkSignatureValidator('key');
        $actual = PostLinkSignatureValidator::create('key');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider dataProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function testValidate(string $key, array $data): void
    {
        $validator = new PostLinkSignatureValidator($key);
        $hash = PostLinkSignatureCalculator::create($key)->calculate($data);
        $validator->validate($data, $hash);

        $this->assertTrue(true);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider validationDataProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function testValidateData(string $key, array $data, string $signatureKeyName): void
    {
        $validator = new PostLinkSignatureValidator($key);
        $hash = PostLinkSignatureCalculator::create($key)->calculate($data);

        $validator->validateData($data + [$signatureKeyName => $hash], $signatureKeyName);
        $this->assertTrue(true);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider differentSignatureKeyNamesProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function testValidateDataWithOtherSignatureKeyName(string $signatureKeyName, array $data): void
    {
        $key = 'test';
        $validator = new PostLinkSignatureValidator($key);

        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Known signature not found');

        $validator->validateData($data, $signatureKeyName);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider dataProvider
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    public function testInvalidSignature(string $key, array $data): void
    {
        $validator = new PostLinkSignatureValidator($key);
        $hash = PostLinkSignatureCalculator::create('otherKey')->calculate($data);

        $this->expectException(InvalidSignatureException::class);

        $validator->validate($data, $hash);
    }

    public function dataProvider(): array
    {
        return [
            ['test', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 100]],
            ['test', ['billNumber' => '200', 'status' => 'preapproved', 'preappId' => 'test123', 'scoring' => 200]],

            ['test', ['status' => 'declined', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 0]],
            ['test', ['billNumber' => '200', 'preappId' => 'test123', 'status' => 'declined', 'scoring' => 0]],

            ['test', ['status' => 'completed', 'billNumber' => '100', 'preappId' => 'test']],
            ['test', ['preappId' => 'test123', 'status' => 'completed', 'billNumber' => '200']],

            ['test', ['billNumber' => '1', 'preappId' => 'test', 'status' => 'preapproved', 'scoring' => 100, 'field' => true]],
            ['test', ['billNumber' => '2', 'preappId' => '123test', 'scoring' => 0, 'status' => 'declined', 'field' => false]],
            ['test', ['field' => null, 'billNumber' => '3', 'preappId' => '111test123', 'status' => 'completed']],

            ['key', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 100]],
            ['key', ['billNumber' => '200', 'status' => 'preapproved', 'preappId' => 'test123', 'scoring' => 200]],

            ['key', ['status' => 'declined', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 0]],
            ['key', ['billNumber' => '200', 'preappId' => 'test123', 'status' => 'declined', 'scoring' => 0]],

            ['key', ['status' => 'completed', 'billNumber' => '100', 'preappId' => 'test']],
            ['key', ['preappId' => 'test123', 'status' => 'completed', 'billNumber' => '200']],

            ['key', ['billNumber' => '1', 'preappId' => 'test', 'status' => 'preapproved', 'scoring' => 100, 'field' => true]],
            ['key', ['billNumber' => '2', 'preappId' => '123test', 'scoring' => 0, 'status' => 'declined', 'field' => false]],
            ['key', ['field' => null, 'billNumber' => '3', 'preappId' => '111test123', 'status' => 'completed']],
        ];
    }

    public function validationDataProvider(): array
    {
        $result = [];
        $keys = ['signature', 'hash'];

        foreach ($keys as $key) {
            foreach ($this->dataProvider() as $item) {
                $item[] = $key;
                $result[] = $item;
            }
        }

        return $result;
    }

    public function differentSignatureKeyNamesProvider(): array
    {
        return [
            ['signature', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test']],
            ['signature', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'hash' => 'test']],
            ['hash', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test']],
            ['hash', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'signature' => 'test']],
        ];
    }
}

