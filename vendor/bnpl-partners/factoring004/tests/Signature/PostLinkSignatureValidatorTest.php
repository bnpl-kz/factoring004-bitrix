<?php

namespace BnplPartners\Factoring004\Signature;

use BnplPartners\Factoring004\Exception\InvalidSignatureException;
use BnplPartners\Factoring004\AbstractTestCase;

class PostLinkSignatureValidatorTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreate()
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
     * @return void
     * @param string $key
     */
    public function testValidate($key, array $data)
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
     * @return void
     * @param string $key
     * @param string $signatureKeyName
     */
    public function testValidateData($key, array $data, $signatureKeyName)
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
     * @return void
     * @param string $signatureKeyName
     */
    public function testValidateDataWithOtherSignatureKeyName($signatureKeyName, array $data)
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
     * @return void
     * @param string $key
     */
    public function testInvalidSignature($key, array $data)
    {
        $validator = new PostLinkSignatureValidator($key);
        $hash = PostLinkSignatureCalculator::create('otherKey')->calculate($data);

        $this->expectException(InvalidSignatureException::class);

        $validator->validate($data, $hash);
    }

    /**
     * @return mixed[]
     */
    public function dataProvider()
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

    /**
     * @return mixed[]
     */
    public function validationDataProvider()
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

    /**
     * @return mixed[]
     */
    public function differentSignatureKeyNamesProvider()
    {
        return [
            ['signature', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test']],
            ['signature', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'hash' => 'test']],
            ['hash', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test']],
            ['hash', ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'signature' => 'test']],
        ];
    }
}

