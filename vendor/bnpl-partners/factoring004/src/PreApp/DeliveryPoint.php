<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;

class DeliveryPoint implements ArrayInterface
{
    /**
     * @var string
     */
    private $region = '';
    /**
     * @var string
     */
    private $city = '';
    /**
     * @var string
     */
    private $district = '';
    /**
     * @var string
     */
    private $street = '';
    /**
     * @var string
     */
    private $house = '';
    /**
     * @var string
     */
    private $flat = '';

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
    public static function createFromArray($deliveryPoint): DeliveryPoint
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

    /**
     * @param string $region
     */
    public function setRegion($region): DeliveryPoint
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @param string $city
     */
    public function setCity($city): DeliveryPoint
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @param string $district
     */
    public function setDistrict($district): DeliveryPoint
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @param string $street
     */
    public function setStreet($street): DeliveryPoint
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @param string $house
     */
    public function setHouse($house): DeliveryPoint
    {
        $this->house = $house;
        return $this;
    }

    /**
     * @param string $flat
     */
    public function setFlat($flat): DeliveryPoint
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
