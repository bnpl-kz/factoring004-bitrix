<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;

class DeliveryPoint implements ArrayInterface
{
    private string $region = '';
    private string $city = '';
    private string $district = '';
    private string $street = '';
    private string $house = '';
    private string $flat = '';

    /**
     * @param array<string, string> $deliveryPoint
     * @psalm-param array{
           region?: string,
           city?: string,
           district?: string,
           street?: string,
           house?: string,
           flat?: string
       } $deliveryPoint
     */
    public static function createFromArray(array $deliveryPoint): DeliveryPoint
    {
        $self = new self();

        foreach ($deliveryPoint as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($self, $method)) {
                $self->{$method}($value);
            }
        }

        return $self;
    }

    public function setRegion(string $region): DeliveryPoint
    {
        $this->region = $region;
        return $this;
    }

    public function setCity(string $city): DeliveryPoint
    {
        $this->city = $city;
        return $this;
    }

    public function setDistrict(string $district): DeliveryPoint
    {
        $this->district = $district;
        return $this;
    }

    public function setStreet(string $street): DeliveryPoint
    {
        $this->street = $street;
        return $this;
    }

    public function setHouse(string $house): DeliveryPoint
    {
        $this->house = $house;
        return $this;
    }

    public function setFlat(string $flat): DeliveryPoint
    {
        $this->flat = $flat;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function getHouse(): string
    {
        return $this->house;
    }

    public function getFlat(): string
    {
        return $this->flat;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{
          region: string,
          city: string,
          district: string,
          street: string,
          house: string,
          flat: string
       }
     */
    public function toArray(): array
    {
        return [
            'region' => $this->getRegion(),
            'city' => $this->getCity(),
            'district' => $this->getDistrict(),
            'street' => $this->getStreet(),
            'house' => $this->getHouse(),
            'flat' => $this->getFlat(),
        ];
    }
}
