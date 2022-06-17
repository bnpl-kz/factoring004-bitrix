<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PreAppMessageTest extends TestCase
{
    public const REQUIRED_DATA = [
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
        'partnerEmail' => 'test@example.com',
        'partnerWebsite' => 'http://example.com',
    ];

    private PreAppMessage $message;

    protected function setUp(): void
    {
        parent::setUp();

        $this->message = new PreAppMessage(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            '1',
            6000,
            1,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])],
            'test@example.com',
            'http://example.com',
        );
    }

    /**
     * @testWith [0]
     *           [-1]
     */
    public function testBillAmountIsPositiveInt(int $billAmount): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            '1',
            $billAmount,
            1,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])],
            'test@example.com',
            'http://example.com',
        );
    }

    /**
     * @testWith [0]
     *           [-1]
     */
    public function testItemsQuantityIsPositiveInt(int $itemsQuantity): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            '1',
            6000,
            $itemsQuantity,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])],
            'test@example.com',
            'http://example.com',
        );
    }

    /**
     * @testWith ["test"]
     *           ["test@"]
     *           ["test@example"]
     *           ["test@localhost"]
     */
    public function testPartnerEmailIsNotValidEmail(string $partnerEmail): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(
            new PartnerData('a', 'b', 'c'),
            '1',
            6000,
            1,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])],
            $partnerEmail,
            'http://example.com',
        );
    }

    /**
     * @testWith ["test"]
     *           ["localhost"]
     */
    public function testPartnerWebsiteIsNotValidDomain(string $partnerWebsite): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PreAppMessage(
            new PartnerData('a', 'b', 'c'),
            '1',
            6000,
            1,
            'http://example.com/success',
            'http://example.com/internal',
            [Item::createFromArray(static::REQUIRED_DATA['items'][0])],
            'test@example.com',
            $partnerWebsite,
        );
    }

    public function testCreateFromArray(): void
    {
        $data = PreAppMessage::createFromArray(static::REQUIRED_DATA);

        $this->assertEquals($this->message, $data);
    }

    public function testCreateFromArrayWithOptionalData(): void
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
     */
    public function testCreateFromArrayFailed(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);

        PreAppMessage::createFromArray($data);
    }

    public function testGetPartnerData(): void
    {
        $this->assertEquals(
            new PartnerData('a', 'b', 'c', 'test@example.com', 'http://example.com'),
            $this->message->getPartnerData(),
        );
    }

    public function testGetBillNumber(): void
    {
        $this->assertEquals('1', $this->message->getBillNumber());
    }

    public function testGetBillAmount(): void
    {
        $this->assertEquals(6000, $this->message->getBillAmount());
    }

    public function testGetItemsQuantity(): void
    {
        $this->assertEquals(1, $this->message->getItemsQuantity());
    }

    public function testGetSuccessRedirect(): void
    {
        $this->assertEquals('http://example.com/success', $this->message->getSuccessRedirect());
    }

    public function testGetPostLink(): void
    {
        $this->assertEquals('http://example.com/internal', $this->message->getPostLink());
    }

    public function testGetFailRedirect(): void
    {
        $this->assertEmpty($this->message->getFailRedirect());
    }

    public function testSetFailRedirect(): void
    {
        $this->message->setFailRedirect('http://example.com/failed');
        $this->assertEquals('http://example.com/failed', $this->message->getFailRedirect());
    }

    public function testGetPhoneNumber(): void
    {
        $this->assertEmpty($this->message->getPhoneNumber());
    }

    public function testSetPhoneNumber(): void
    {
        $this->message->setPhoneNumber('77771234567');
        $this->assertEquals('77771234567', $this->message->getPhoneNumber());

        $this->message->setPhoneNumber('77770000000');
        $this->assertEquals('77770000000', $this->message->getPhoneNumber());

        $this->expectException(InvalidArgumentException::class);
        $this->message->setPhoneNumber('77absc789');
    }

    public function testGetExpiresAt(): void
    {
        $this->assertNull($this->message->getExpiresAt());
    }

    public function testSetExpiresAt(): void
    {
        $expiresAt = new DateTime();
        $this->message->setExpiresAt($expiresAt);
        $this->assertEquals($expiresAt, $this->message->getExpiresAt());
    }

    public function testGetDeliveryDate(): void
    {
        $this->assertNull($this->message->getDeliveryDate());
    }

    public function testSetDeliveryDate(): void
    {
        $deliveryDate = new DateTime();
        $this->message->setDeliveryDate($deliveryDate);
        $this->assertEquals($deliveryDate, $this->message->getDeliveryDate());
    }

    public function testGetDeliveryPoint(): void
    {
        $this->assertEmpty($this->message->getDeliveryPoint());
    }

    public function testSetDeliveryPoint(): void
    {
        $deliveryPoint = new DeliveryPoint();
        $this->message->setDeliveryPoint($deliveryPoint);
        $this->assertEquals($deliveryPoint, $this->message->getDeliveryPoint());
    }

    public function testGetPartnerEmail(): void
    {
        $this->assertEquals('test@example.com', $this->message->getPartnerEmail());
    }

    public function testGetPartnerWebsite(): void
    {
        $this->assertEquals('http://example.com', $this->message->getPartnerWebsite());
    }

    public function testGetItems(): void
    {
        $this->assertEquals([Item::createFromArray(static::REQUIRED_DATA['items'][0])], $this->message->getItems());
    }

    public function testToArray(): void
    {
        $expected = static::REQUIRED_DATA;
        $this->assertEquals($expected, $this->message->toArray());

        $this->message->setFailRedirect('http://example.com/failed');
        $this->message->setPhoneNumber('77771234567');
        $this->message->setExpiresAt($expiresAt = new DateTime());
        $this->message->setDeliveryDate($deliveryDate = new DateTime());
        $this->message->setDeliveryPoint(DeliveryPoint::createFromArray(['flat' => '10', 'house' => '15']));

        $expected = static::REQUIRED_DATA + [
            'failRedirect' => 'http://example.com/failed',
            'phoneNumber' => '77771234567',
            'expiresAt' => $expiresAt->format(DateTimeInterface::ISO8601),
            'deliveryDate' => $deliveryDate->format(DateTimeInterface::ISO8601),
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
        $this->assertEquals($expected, $this->message->toArray());
    }

    public function invalidArraysProvider(): array
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
}

