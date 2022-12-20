<?php

namespace BnplPartners\Factoring004\Signature;

use BnplPartners\Factoring004\AbstractTestCase;

class PostLinkSignatureCalculatorTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreate()
    {
        $expected = new PostLinkSignatureCalculator('test');
        $actual = PostLinkSignatureCalculator::create('test');
        $this->assertEquals($expected, $actual);

        $expected = new PostLinkSignatureCalculator('key');
        $actual = PostLinkSignatureCalculator::create('key');
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider dataProvider
     * @return void
     * @param string $key
     * @param string $str
     */
    public function testCalculate($key, $str, array $data)
    {
        $builder = new PostLinkSignatureCalculator($key);
        $hash = hash_hmac('sha512', $str, $key);

        $this->assertTrue(hash_equals($hash, $builder->calculate($data)));
    }

    /**
     * @return mixed[]
     */
    public function dataProvider()
    {
        return [
            [
                'key',
                'status:preapproved:preappId:test:billNumber:100',
                ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 100],
            ],
            [
                'key',
                'status:declined:preappId:test:billNumber:100',
                ['status' => 'declined', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 0],
            ],
            [
                'key',
                'status:declined:preappId:test123:billNumber:200',
                ['billNumber' => '200', 'preappId' => 'test123', 'status' => 'declined', 'scoring' => 0],
            ],
            [
                'key',
                'status:completed:preappId:test:billNumber:100',
                ['status' => 'completed', 'billNumber' => '100', 'preappId' => 'test'],
            ],
            [
                'key',
                'status:completed:preappId:test123:billNumber:200',
                ['preappId' => 'test123', 'status' => 'completed', 'billNumber' => '200'],
            ],
            [
                'key',
                'status:preapproved:preappId:test:billNumber:1',
                ['billNumber' => '1', 'preappId' => 'test', 'status' => 'preapproved', 'scoring' => 100, 'field' => true],
            ],
            [
                'key',
                'status:declined:preappId:123test:billNumber:2',
                ['billNumber' => '2', 'preappId' => '123test', 'scoring' => 0, 'status' => 'declined', 'field' => false],
            ],
            [
                'key',
                'status:completed:preappId:111test123:billNumber:3',
                ['field' => null, 'billNumber' => '3', 'preappId' => '111test123', 'status' => 'completed'],
            ],

            [
                'test',
                'status:preapproved:preappId:test:billNumber:100',
                ['status' => 'preapproved', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 100],
            ],
            [
                'test',
                'status:declined:preappId:test:billNumber:100',
                ['status' => 'declined', 'billNumber' => '100', 'preappId' => 'test', 'scoring' => 0],
            ],
            [
                'test',
                'status:declined:preappId:test123:billNumber:200',
                ['billNumber' => '200', 'preappId' => 'test123', 'status' => 'declined', 'scoring' => 0],
            ],
            [
                'test',
                'status:completed:preappId:test:billNumber:100',
                ['status' => 'completed', 'billNumber' => '100', 'preappId' => 'test'],
            ],
            [
                'test',
                'status:completed:preappId:test123:billNumber:200',
                ['preappId' => 'test123', 'status' => 'completed', 'billNumber' => '200'],
            ],
            [
                'test',
                'status:preapproved:preappId:test:billNumber:1',
                ['billNumber' => '1', 'preappId' => 'test', 'status' => 'preapproved', 'scoring' => 100, 'field' => true],
            ],
            [
                'test',
                'status:declined:preappId:123test:billNumber:2',
                ['billNumber' => '2', 'preappId' => '123test', 'scoring' => 0, 'status' => 'declined', 'field' => false],
            ],
            [
                'test',
                'status:completed:preappId:111test123:billNumber:3',
                ['field' => null, 'billNumber' => '3', 'preappId' => '111test123', 'status' => 'completed'],
            ],
        ];
    }
}

