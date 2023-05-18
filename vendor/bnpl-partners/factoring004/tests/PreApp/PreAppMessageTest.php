<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\AbstractTestCase;
use DateTime;
use InvalidArgumentException;

class PreAppMessageTest extends AbstractTestCase
{
    const REQUIRED_DATA = [
        'partnerData' => [
            'partnerName' => 'a',
            'partnerCode' => 'b',
            'pointCode' => 'c',
            'partnerEmail' => 'test@example.com',
            'partnerWebsite' => 'http://example.com',
        ],
        'billNumber' => '1',
        'billAmount' => 6000,
        'itemsQuantity' => 1,
        'successRedirect' => 'http://example.com/success',
        'postLink' => 'http://example.com/internal',
        'items' => [
            [
                'itemId' => '1',
                'itemName' => 'test',
                'itemCategory' => '1',
                'itemQuantity' => 1,
                'itemPrice' => 6000,
                'itemSum' => 8000,
            ],
        ],
    ];

    /**
     * @testWith [0]
     *           [-1]
     * @return void
     * @param int $billAmount
     */
    public function testBillAmountIsPositiveInt($billAmount)
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'), '1', $billAmount, 1, 'http://example.com/success', 'http://example.com/internal', [Item::createFromArray(static::REQUIRED_DATA['items'][0])]);
    }

    /**
     * @testWith [0]
     *           [-1]
     * @return void
     * @param int $itemsQuantity
     */
    public function testItemsQuantityIsPositiveInt($itemsQuantity)
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'), '1', 6000, $itemsQuantity, 'http://example.com/success', 'http://example.com/internal', [Item::createFromArray(static::REQUIRED_DATA['items'][0])]);
    }

    /**
     * @return void
     */
    public function testCreateFromArray()
    {
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA);

        $this->assertEquals($this->getExpectedMessage(), $data);
    }

    /**
     * @return void
     */
    public function testCreateFromArrayWithOptionalData()
    {
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + ['failRedirect' => 'http://example.com/failed']);
        $this->assertEquals('http://example.com/failed', $data->getFailRedirect());

        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + ['phoneNumber' => '77771234567']);
        $this->assertEquals('77771234567', $data->getPhoneNumber());

        $expiresAt = new DateTime();
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + ['expiresAt' => $expiresAt]);
        $this->assertEquals($expiresAt, $data->getExpiresAt());

        $deliveryDate = new DateTime();
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + ['deliveryDate' => $deliveryDate]);
        $this->assertEquals($deliveryDate, $data->getDeliveryDate());

        $deliveryPoint = [];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray([]), $data->getDeliveryPoint());

        $deliveryPoint = ['region' => 'Almaty'];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());

        $deliveryPoint = ['region' => 'Almaty', 'city' => 'Almaty'];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());

        $deliveryPoint = ['region' => 'Almaty', 'city' => 'Almaty', 'district' => 'Almaly'];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());

        $deliveryPoint = ['region' => 'Almaty', 'city' => 'Almaty', 'district' => 'Almaly', 'street' => 'Green'];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());

        $deliveryPoint = [
            'region' => 'Almaty',
            'city' => 'Almaty',
            'district' => 'Almaly',
            'street' => 'Green',
            'house' => '10/15'
        ];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());

        $deliveryPoint = [
            'region' => 'Almaty',
            'city' => 'Almaty',
            'district' => 'Almaly',
            'street' => 'Green',
            'house' => '10/15',
            'flat' => '70'
        ];
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA + compact('deliveryPoint'));
        $this->assertEquals(DeliveryPoint::createFromArray($deliveryPoint), $data->getDeliveryPoint());
    }

    /**
     * @param array<string, mixed> $data
     *
     * @dataProvider invalidArraysProvider
     * @return void
     */
    public function testCreateFromArrayFailed(array $data)
    {
        $this->expectException(InvalidArgumentException::class);

        PreAppMessage::createFromArray($data);
    }

    /**
     * @return void
     */
    public function testGetPartnerData()
    {
        $this->assertEquals(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            $this->getExpectedMessage()->getPartnerData()
        );
    }

    /**
     * @return void
     */
    public function testGetBillNumber()
    {
        $this->assertEquals('1', $this->getExpectedMessage()->getBillNumber());
    }

    /**
     * @return void
     */
    public function testGetBillAmount()
    {
        $this->assertEquals(6000, $this->getExpectedMessage()->getBillAmount());
    }

    /**
     * @return void
     */
    public function testGetItemsQuantity()
    {
        $this->assertEquals(1, $this->getExpectedMessage()->getItemsQuantity());
    }

    /**
     * @return void
     */
    public function testGetSuccessRedirect()
    {
        $this->assertEquals('http://example.com/success', $this->getExpectedMessage()->getSuccessRedirect());
    }

    /**
     * @return void
     */
    public function testGetPostLink()
    {
        $this->assertEquals('http://example.com/internal', $this->getExpectedMessage()->getPostLink());
    }

    /**
     * @return void
     */
    public function testGetFailRedirect()
    {
        $this->assertEmpty($this->getExpectedMessage()->getFailRedirect());
    }

    /**
     * @return void
     */
    public function testSetFailRedirect()
    {
        $expected = $this->getExpectedMessage()->setFailRedirect('http://example.com/failed');
        $this->assertEquals('http://example.com/failed', $expected->getFailRedirect());
    }

    /**
     * @return void
     */
    public function testGetPhoneNumber()
    {
        $this->assertEmpty($this->getExpectedMessage()->getPhoneNumber());
    }

    /**
     * @return void
     */
    public function testSetPhoneNumber()
    {
        $expected = $this->getExpectedMessage()->setPhoneNumber('77771234567');
        $this->assertEquals('77771234567', $expected->getPhoneNumber());

        $expected = $this->getExpectedMessage()->setPhoneNumber('77770000000');
        $this->assertEquals('77770000000', $expected->getPhoneNumber());

        $this->expectException(InvalidArgumentException::class);
        $this->getExpectedMessage()->setPhoneNumber('77absc789');
    }

    /**
     * @return void
     */
    public function testGetExpiresAt()
    {
        $this->assertNull($this->getExpectedMessage()->getExpiresAt());
    }

    /**
     * @return void
     */
    public function testSetExpiresAt()
    {
        $expiresAt = new DateTime();
        $expected = $this->getExpectedMessage()->setExpiresAt($expiresAt);
        $this->assertEquals($expiresAt, $expected->getExpiresAt());
    }

    /**
     * @return void
     */
    public function testGetDeliveryDate()
    {
        $this->assertNull($this->getExpectedMessage()->getDeliveryDate());
    }

    /**
     * @return void
     */
    public function testSetDeliveryDate()
    {
        $deliveryDate = new DateTime();
        $expected = $this->getExpectedMessage()->setDeliveryDate($deliveryDate);
        $this->assertEquals($deliveryDate, $expected->getDeliveryDate());
    }

    /**
     * @return void
     */
    public function testGetDeliveryPoint()
    {
        $this->assertEmpty($this->getExpectedMessage()->getDeliveryPoint());
    }

    /**
     * @return void
     */
    public function testSetDeliveryPoint()
    {
        $deliveryPoint = new DeliveryPoint();
        $expected = $this->getExpectedMessage()->setDeliveryPoint($deliveryPoint);
        $this->assertEquals($deliveryPoint, $expected->getDeliveryPoint());
    }

    /**
     * @return void
     */
    public function testGetItems()
    {
        $this->assertEquals([Item::createFromArray(static::REQUIRED_DATA['items'][0])], $this->getExpectedMessage()->getItems());
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $expected = static::REQUIRED_DATA;
        $this->assertEquals($expected, $this->getExpectedMessage()->toArray());

        $message = $this->getExpectedMessage()
            ->setFailRedirect('http://example.com/failed')
            ->setPhoneNumber('77771234567')
            ->setExpiresAt($expiresAt = new DateTime())
            ->setDeliveryDate($deliveryDate = new DateTime())
            ->setDeliveryPoint(DeliveryPoint::createFromArray(['flat' => '10', 'house' => '15']));

        $expected = static::REQUIRED_DATA + [
            'failRedirect' => 'http://example.com/failed',
            'phoneNumber' => '77771234567',
            'expiresAt' => $expiresAt->format('Y-m-d\TH:i:sO'),
            'deliveryDate' => $deliveryDate->format('Y-m-d\TH:i:sO'),
            'deliveryPoint' => [
                'flat' => '10',
                'house' => '15',
                'street' => '',
                'city' => '',
                'district' => '',
                'region' => '',
            ],
            'items' => static::REQUIRED_DATA['items'],
        ];
        $this->assertEquals($expected, $message->toArray());
    }

    /**
     * @return mixed[]
     */
    public function invalidArraysProvider()
    {
        return [
            [[]],
            [['partnerData' => []]],
            [['partnerData' => [], 'billNumber' => '1']],
            [['partnerData' => [], 'billNumber' => '1', 'billAmount' => 6000]],
            [[
                'partnerData' => [],
                'billNumber' => '1',
                'billAmount' => 6000,
                'itemsQuantity' => 1,
                'successRedirect' => 'http://example.com/success',
            ]],
            [[
                'partnerData' => [],
                'billNumber' => '1',
                'billAmount' => 6000,
                'itemsQuantity' => 1,
                'successRedirect' => 'http://example.com/success',
                'postLink' => 'http://example.com/internal',
            ]],
        ];
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    private function getExpectedMessage()
    {
        return  new PreAppMessage(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            '1',
            6000,
            1,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])]
        );
    }
}

