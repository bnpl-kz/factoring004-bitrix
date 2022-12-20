<?php

namespace BnplPartners\Factoring004\PreApp;

use BnplPartners\Factoring004\ArrayInterface;
use InvalidArgumentException;

/**
 * @psalm-immutable
 */
class PartnerData implements ArrayInterface
{
    /**
     * @var string
     */
    private $partnerName;
    /**
     * @var string
     */
    private $partnerCode;
    /**
     * @var string
     */
    private $pointCode;
    /**
     * @var string|null
     */
    private $partnerEmail;
    /**
     * @var string|null
     */
    private $partnerWebsite;

    /**
     * @param string $partnerName
     * @param string $partnerCode
     * @param string $pointCode
     * @param string $partnerEmail
     * @param string $partnerWebsite
     */
    public function __construct(
        $partnerName,
        $partnerCode,
        $pointCode,
        $partnerEmail = null,
        $partnerWebsite = null
    ) {
        $this->partnerName = $partnerName;
        $this->partnerCode = $partnerCode;
        $this->pointCode = $pointCode;
        $this->partnerEmail = $partnerEmail;
        $this->partnerWebsite = $partnerWebsite;
    }

    /**
    * @param array<string, string> $partnerData
    * @psalm-param array{
          partnerName: string,
          partnerCode: string,
          pointCode: string,
          partnerEmail?: string|null,
          partnerWebsite?: string|null,
     } $partnerData
    *
    * @throws \InvalidArgumentException
     * @return \BnplPartners\Factoring004\PreApp\PartnerData
    */
    public static function createFromArray(array $partnerData)
    {
        if (empty($partnerData['partnerName'])) {
            throw new InvalidArgumentException("Key 'partnerName' is required");
        }

        if (empty($partnerData['partnerCode'])) {
            throw new InvalidArgumentException("Key 'partnerCode' is required");
        }

        if (empty($partnerData['pointCode'])) {
            throw new InvalidArgumentException("Key 'pointCode' is required");
        }

        return new self($partnerData['partnerName'], $partnerData['partnerCode'], $partnerData['pointCode'], isset($partnerData['partnerEmail']) ? $partnerData['partnerEmail'] : null, isset($partnerData['partnerWebsite']) ? $partnerData['partnerWebsite'] : null);
    }

    /**
     * @return string
     */
    public function getPartnerName()
    {
        return $this->partnerName;
    }

    /**
     * @return string
     */
    public function getPartnerCode()
    {
        return $this->partnerCode;
    }

    /**
     * @return string
     */
    public function getPointCode()
    {
        return $this->pointCode;
    }

    /**
     * @return string|null
     */
    public function getPartnerEmail()
    {
        return $this->partnerEmail;
    }

    /**
     * @return string|null
     */
    public function getPartnerWebsite()
    {
        return $this->partnerWebsite;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{
         partnerName: string,
         partnerCode: string,
         pointCode: string,
         partnerEmail?: string,
         partnerWebsite?: string,
       }
     */
    public function toArray()
    {
        $data = [
            'partnerName' => $this->getPartnerName(),
            'partnerCode' => $this->getPartnerCode(),
            'pointCode' => $this->getPointCode(),
        ];

        $partnerEmail = $this->getPartnerEmail();
        $partnerWebsite = $this->getPartnerWebsite();

        if ($partnerEmail) {
            $data['partnerEmail'] = $partnerEmail;
        }

        if ($partnerWebsite) {
            $data['partnerWebsite'] = $partnerWebsite;
        }

        return $data;
    }
}
