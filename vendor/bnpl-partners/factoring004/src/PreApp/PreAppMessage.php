<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;
use DateTimeInterface;
use InvalidArgumentException;

class PreAppMessage implements ArrayInterface
{
    /**
     * @var \BnplPartners\Factoring004\PreApp\PartnerData
     */
    private $partnerData;
    /**
     * @var string
     */
    private $billNumber;
    /**
     * @var int
     */
    private $billAmount;
    /**
     * @var int
     */
    private $itemsQuantity;
    /**
     * @var string
     */
    private $successRedirect;
    /**
     * @var string
     */
    private $failRedirect = '';
    /**
     * @var string
     */
    private $postLink;
    /**
     * @var string
     */
    private $phoneNumber = '';
    /**
     * @var \DateTimeInterface|null
     */
    private $expiresAt;
    /**
     * @var \DateTimeInterface|null
     */
    private $deliveryDate;
    /**
     * @var \BnplPartners\Factoring004\PreApp\DeliveryPoint|null
     */
    private $deliveryPoint;

    /**
     * @var \BnplPartners\Factoring004\PreApp\Item[]
     */
    private $items;

    /**
     * @param \BnplPartners\Factoring004\PreApp\Item[] $items
     */
    public function __construct(
        PartnerData $partnerData,
        string $billNumber,
        int $billAmount,
        int $itemsQuantity,
        string $successRedirect,
        string $postLink,
        array $items
    ) {
        if ($billAmount <= 0) {
            throw new InvalidArgumentException('billAmount must be greater than 0');
        }

        if ($itemsQuantity <= 0) {
            throw new InvalidArgumentException('itemsQuantity must be greater than 0');
        }

        $this->partnerData = $partnerData;
        $this->billNumber = $billNumber;
        $this->billAmount = $billAmount;
        $this->itemsQuantity = $itemsQuantity;
        $this->successRedirect = $successRedirect;
        $this->postLink = $postLink;
        $this->items = $items;
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{
           partnerData: array{
               partnerName: string,
               partnerCode: string,
               pointCode: string,
               partnerEmail: string,
               partnerWebsite: string,
           },
           billNumber: string,
           billAmount: int,
           itemsQuantity: int,
           successRedirect: string,
           failRedirect?: string,
           postLink: string,
           phoneNumber?: string,
           expiresAt?: \DateTimeInterface,
           deliveryDate?: \DateTimeInterface,
           deliveryPoint?: array{
               region?: string,
               city?: string,
               district?: string,
               street?: string,
               house?: string,
               flat?: string,
           },
           items: array{
              itemId: string,
              itemName: string,
              itemCategory?: string,
              itemQuantity: int,
              itemPrice: int,
              itemSum: int,
           }[],
     * } $data
     *
     * @throws \InvalidArgumentException
     */
    public static function createFromArray($data): PreAppMessage
    {
        $requiredKeys = ['partnerData', 'billNumber', 'billAmount', 'itemsQuantity', 'successRedirect', 'postLink'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Key '$key' is required");
            }
        }

        $object = new self(
            PartnerData::createFromArray($data['partnerData']),
            $data['billNumber'],
            $data['billAmount'],
            $data['itemsQuantity'],
            $data['successRedirect'],
            $data['postLink'],
            array_map(function (array $item) {
                return Item::createFromArray($item);
            }, $data['items'])
        );

        if (isset($data['failRedirect'])) {
            $object->setFailRedirect($data['failRedirect']);
        }

        if (isset($data['phoneNumber'])) {
            $object->setPhoneNumber($data['phoneNumber']);
        }

        if (isset($data['expiresAt'])) {
            $object->setExpiresAt($data['expiresAt']);
        }

        if (isset($data['deliveryDate'])) {
            $object->setDeliveryDate($data['deliveryDate']);
        }

        if (isset($data['deliveryPoint'])) {
            $object->setDeliveryPoint(DeliveryPoint::createFromArray($data['deliveryPoint']));
        }

        return $object;
    }

    /**
     * @param string $failRedirect
     */
    public function setFailRedirect($failRedirect): PreAppMessage
    {
        $this->failRedirect = $failRedirect;
        return $this;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber($phoneNumber): PreAppMessage
    {
        if (!preg_match('/^77\d{9}$/', $phoneNumber)) {
            throw new InvalidArgumentException('phoneNumber is invalid');
        }

        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param \BnplPartners\Factoring004\PreApp\DeliveryPoint $deliveryPoint
     */
    public function setDeliveryPoint($deliveryPoint): PreAppMessage
    {
        $this->deliveryPoint = $deliveryPoint;
        return $this;
    }

    /**
     * @param \DateTimeInterface $expiresAt
     */
    public function setExpiresAt($expiresAt): PreAppMessage
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @param \DateTimeInterface $deliveryDate
     */
    public function setDeliveryDate($deliveryDate): PreAppMessage
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    public function getPartnerData(): PartnerData
    {
        return $this->partnerData;
    }

    public function getBillNumber(): string
    {
        return $this->billNumber;
    }

    public function getBillAmount(): int
    {
        return $this->billAmount;
    }

    public function getItemsQuantity(): int
    {
        return $this->itemsQuantity;
    }

    public function getSuccessRedirect(): string
    {
        return $this->successRedirect;
    }

    public function getFailRedirect(): string
    {
        return $this->failRedirect;
    }

    public function getPostLink(): string
    {
        return $this->postLink;
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint|null
     */
    public function getDeliveryPoint()
    {
        return $this->deliveryPoint;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $expiresAt = $this->getExpiresAt();
        $deliveryDate = $this->getDeliveryDate();
        $deliveryPoint = $this->getDeliveryPoint();

        return array_filter([
            'partnerData' => $this->getPartnerData()->toArray(),
            'billNumber' => $this->getBillNumber(),
            'billAmount' => $this->getBillAmount(),
            'itemsQuantity' => $this->getItemsQuantity(),
            'successRedirect' => $this->getSuccessRedirect(),
            'postLink' => $this->getPostLink(),
            'expiresAt' => $expiresAt === null ? null : $expiresAt->format('Y-m-d\TH:i:sO'),
            'deliveryDate' => $deliveryDate === null ? null : $deliveryDate->format('Y-m-d\TH:i:sO'),
            'failRedirect' => $this->getFailRedirect(),
            'phoneNumber' => $this->getPhoneNumber(),
            'deliveryPoint' => $deliveryPoint ? $deliveryPoint->toArray() : null,
            'items' => array_map(function (Item $item) {
                return $item->toArray();
            }, $this->getItems()),
        ]);
    }
}
