<?php

namespace BnplPartners\Factoring004\PreApp;

use InvalidArgumentException;
use BnplPartners\Factoring004\AbstractTestCase;

class PartnerDataTest extends AbstractTestCase
{
    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $expected = new PartnerData('a', 'b', 'c');
        $actual = PartnerData::createFromArray([
            'partnerName' => 'a',
            'partnerCode' => 'b',
            'pointCode' => 'c',
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new PartnerData('name', 'code', 'test', 'test@example.org', 'http://example.org');
        $actual = PartnerData::createFromArray([
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
            'partnerEmail' => 'test@example.org',
            'partnerWebsite' => 'http://example.org',
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new PartnerData('name', 'code', 'test', '', '');
        $actual = PartnerData::createFromArray([
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
            'partnerEmail' => '',
            'partnerWebsite' => '',
        ]);
        $this->assertEquals($expected, $actual);

        $expected = new PartnerData('name', 'code', 'test', null, null);
        $actual = PartnerData::createFromArray([
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
            'partnerEmail' => null,
            'partnerWebsite' => null,
        ]);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<string, string> $partnerData
     *
     * @dataProvider invalidArraysProvider
     * @return void
     */
    public function testCreateFromArrayFailed(array $partnerData)
    {
        $this->expectException(InvalidArgumentException::class);

        PartnerData::createFromArray($partnerData);
    }

    /**
     * @return void
     */
    public function testGetPartnerName()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('a', $partnerData->getPartnerName());

        $partnerData = new PartnerData('test', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('test', $partnerData->getPartnerName());
    }

    /**
     * @return void
     */
    public function testGetPointCode()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('c', $partnerData->getPointCode());

        $partnerData = new PartnerData('a', 'b', 'test', 'test@example.com', 'http://example.com');
        $this->assertEquals('test', $partnerData->getPointCode());
    }

    /**
     * @return void
     */
    public function testGetPartnerCode()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('b', $partnerData->getPartnerCode());

        $partnerData = new PartnerData('a', 'test', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('test', $partnerData->getPartnerCode());
    }

    /**
     * @return void
     */
    public function testGetPartnerEmail()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('test@example.com', $partnerData->getPartnerEmail());

        $partnerData = new PartnerData('a', 'test', 'c', 'test@example.org', 'http://example.com');
        $this->assertEquals('test@example.org', $partnerData->getPartnerEmail());

        $partnerData = new PartnerData('a', 'test', 'c', '', '');
        $this->assertEmpty($partnerData->getPartnerEmail());

        $partnerData = new PartnerData('a', 'test', 'c');
        $this->assertNull($partnerData->getPartnerEmail());
    }

    /**
     * @return void
     */
    public function testGetPartnerWebsite()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $this->assertEquals('http://example.com', $partnerData->getPartnerWebsite());

        $partnerData = new PartnerData('a', 'test', 'c', 'test@example.com', 'http://example.org');
        $this->assertEquals('http://example.org', $partnerData->getPartnerWebsite());

        $partnerData = new PartnerData('a', 'test', 'c', '', '');
        $this->assertEmpty($partnerData->getPartnerWebsite());

        $partnerData = new PartnerData('a', 'test', 'c');
        $this->assertNull($partnerData->getPartnerWebsite());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $partnerData = new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com');
        $expected = [
            'partnerName' => 'a',
            'partnerCode' => 'b',
            'pointCode' => 'c',
            'partnerEmail' => 'test@example.com',
            'partnerWebsite' => 'http://example.com',
        ];
        $this->assertEquals($expected, $partnerData->toArray());

        $partnerData = new PartnerData('name', 'code', 'test', 'test@example.org', 'http://example.org');
        $expected = [
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
            'partnerEmail' => 'test@example.org',
            'partnerWebsite' => 'http://example.org',
        ];
        $this->assertEquals($expected, $partnerData->toArray());

        $partnerData = new PartnerData('name', 'code', 'test', '', '');
        $expected = array_filter([
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
            'partnerEmail' => '',
            'partnerWebsite' => '',
        ]);
        $this->assertEquals($expected, $partnerData->toArray());

        $partnerData = new PartnerData('name', 'code', 'test');
        $expected = [
            'partnerName' => 'name',
            'partnerCode' => 'code',
            'pointCode' => 'test',
        ];
        $this->assertEquals($expected, $partnerData->toArray());
    }

    /**
     * @return mixed[]
     */
    public function invalidArraysProvider()
    {
        return [
            [[]],
            [['partnerName' => 'a']],
            [['partnerCode' => 'b']],
            [['pointCode' => 'c']],
            [['pointEmail' => 'test@example.com']],
            [['pointWebsite' => 'http://example.com']],
            [['partnerName' => 'a', 'partnerCode' => 'b']],
            [['partnerName' => 'a', 'pointCode' => 'c']],
            [['partnerCode' => 'b', 'pointCode' => 'c']],
            [['partnerName' => 'a', 'pointEmail' => 'test@example.com']],
            [['partnerCode' => 'b', 'pointEmail' => 'test@example.com']],
            [['pointCode' => 'c', 'pointEmail' => 'test@example.com']],
            [['pointEmail' => 'test@example.com', 'pointWebsite' => 'http://example.com']],
        ];
    }
}

