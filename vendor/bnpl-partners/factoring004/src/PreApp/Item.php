<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;

class Item implements ArrayInterface
{
    /**
     * @var string
     */
    private $itemId;
    /**
     * @var string
     */
    private $itemName;
    /**
     * @var string|null
     */
    private $itemCategory;
    /**
     * @var int
     */
    private $itemQuantity;
    /**
     * @var int
     */
    private $itemPrice;
    /**
     * @var int
     */
    private $itemSum;

    /**
     * @param string $itemId
     * @param string $itemName
     * @param int $itemQuantity
     * @param int $itemPrice
     * @param int $itemSum
     */
    public function __construct(
        $itemId,
        $itemName,
        $itemQuantity,
        $itemPrice,
        $itemSum
    ) {
        $this->itemId = $itemId;
        $this->itemName = $itemName;
        $this->itemQuantity = $itemQuantity;
        $this->itemPrice = $itemPrice;
        $this->itemSum = $itemSum;
    }

    /**
     * @param array<string, mixed> $item
     * @psalm-param array{
           itemId: string,
           itemName: string,
           itemCategory?: string,
           itemQuantity: int,
           itemPrice: int,
           itemSum: int,
     * } $item
     *
     * @return \BnplPartners\Factoring004\PreApp\Item
     */
    public static function createFromArray(array $item)
    {
        $self = new self($item['itemId'], $item['itemName'], $item['itemQuantity'], $item['itemPrice'], $item['itemSum']);

        if (isset($item['itemCategory'])) {
            $self->setItemCategory($item['itemCategory']);
        }

        return $self;
    }

    /**
     * @return string
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @return string
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     * @return string|null
     */
    public function getItemCategory()
    {
        return $this->itemCategory;
    }

    /**
     * @return int
     */
    public function getItemQuantity()
    {
        return $this->itemQuantity;
    }

    /**
     * @return int
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * @return int
     */
    public function getItemSum()
    {
        return $this->itemSum;
    }

    /**
     * @param string $itemCategory
     * @return \BnplPartners\Factoring004\PreApp\Item
     */
    public function setItemCategory($itemCategory)
    {
        $this->itemCategory = $itemCategory;
        return $this;
    }

    /**
    * @psalm-return array{
          itemId: string,
          itemName: string,
          itemCategory?: string,
          itemQuantity: int,
          itemPrice: int,
          itemSum: int,
    * }
     * @return mixed[]
    */
    public function toArray()
    {
        $data = [
            'itemId' => $this->getItemId(),
            'itemName' => $this->getItemName(),
            'itemQuantity' => $this->getItemQuantity(),
            'itemPrice' => $this->getItemPrice(),
            'itemSum' => $this->getItemSum(),
        ];

        $category = $this->getItemCategory();

        if ($category) {
            $data['itemCategory'] = $category;
        }

        return $data;
    }
}
