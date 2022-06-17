<?php

declare(strict_types=1);

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

    public function __construct(
        string $itemId,
        string $itemName,
        int $itemQuantity,
        int $itemPrice,
        int $itemSum
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
    public static function createFromArray($item): Item
    {
        $self = new self(
            $item['itemId'],
            $item['itemName'],
            $item['itemQuantity'],
            $item['itemPrice'],
            $item['itemSum']
        );

        if (isset($item['itemCategory'])) {
            $self->setItemCategory($item['itemCategory']);
        }

        return $self;
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function getItemName(): string
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

    public function getItemQuantity(): int
    {
        return $this->itemQuantity;
    }

    public function getItemPrice(): int
    {
        return $this->itemPrice;
    }

    public function getItemSum(): int
    {
        return $this->itemSum;
    }

    public function setItemCategory(string $itemCategory): Item
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
     */
    public function toArray(): array
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
