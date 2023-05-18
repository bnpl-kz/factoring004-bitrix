<?php

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
     * @param string $billNumber
     * @param int $billAmount
     * @param int $itemsQuantity
     * @param string $successRedirect
     * @param string $postLink
     */
    public function __construct(
        PartnerData $partnerData,
        $billNumber,
        $billAmount,
        $itemsQuantity,
        $successRedirect,
        $postLink,
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
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
    */
    public static function createFromArray(array $data)
    {
        $requiredKeys = ['partnerData', 'billNumber', 'billAmount', 'itemsQuantity', 'successRedirect', 'postLink'];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new InvalidArgumentException("Key '$key' is required");
            }
        }

        $object = new self(PartnerData::createFromArray($data['partnerData']), $data['billNumber'], $data['billAmount'], $data['itemsQuantity'], $data['successRedirect'], $data['postLink'], array_map(function (array $item) {
            return Item::createFromArray($item);
        }, $data['items']));

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
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    public function setFailRedirect($failRedirect)
    {
        $this->failRedirect = $failRedirect;
        return $this;
    }

    /**
     * @param string $phoneNumber
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    public function setPhoneNumber($phoneNumber)
    {
        if (!preg_match('/^77\d{9}$/', $phoneNumber)) {
            throw new InvalidArgumentException('phoneNumber is invalid');
        }

        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    public function setDeliveryPoint(DeliveryPoint $deliveryPoint)
    {
        $this->deliveryPoint = $deliveryPoint;
        return $this;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    public function setExpiresAt(DateTimeInterface $expiresAt)
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     */
    public function setDeliveryDate(DateTimeInterface $deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
        return $this;
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\PartnerData
     */
    public function getPartnerData()
    {
        return $this->partnerData;
    }

    /**
     * @return string
     */
    public function getBillNumber()
    {
        return $this->billNumber;
    }

    /**
     * @return int
     */
    public function getBillAmount()
    {
        return $this->billAmount;
    }

    /**
     * @return int
     */
    public function getItemsQuantity()
    {
        return $this->itemsQuantity;
    }

    /**
     * @return string
     */
    public function getSuccessRedirect()
    {
        return $this->successRedirect;
    }

    /**
     * @return string
     */
    public function getFailRedirect()
    {
        return $this->failRedirect;
    }

    /**
     * @return string
     */
    public function getPostLink()
    {
        return $this->postLink;
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
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
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
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
