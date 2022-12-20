<?php

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
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
    */
    public static function createFromArray(array $deliveryPoint)
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
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setRegion($region)
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @param string $city
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @param string $district
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setDistrict($district)
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @param string $street
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setStreet($street)
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @param string $house
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setHouse($house)
    {
        $this->house = $house;
        return $this;
    }

    /**
     * @param string $flat
     * @return \BnplPartners\Factoring004\PreApp\DeliveryPoint
     */
    public function setFlat($flat)
    {
        $this->flat = $flat;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getDistrict()
    {
        return $this->district;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * @return string
     */
    public function getFlat()
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
    public function toArray()
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
