<?php

declare(strict_types=1);

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
     * @param string|null $partnerEmail
     * @param string|null $partnerWebsite
     */
    public function __construct(
        string $partnerName,
        string $partnerCode,
        string $pointCode,
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
     */
    public static function createFromArray($partnerData): PartnerData
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

        return new self(
            $partnerData['partnerName'],
            $partnerData['partnerCode'],
            $partnerData['pointCode'],
            $partnerData['partnerEmail'] ?? null,
            $partnerData['partnerWebsite'] ?? null
        );
    }

    public function getPartnerName(): string
    {
        return $this->partnerName;
    }

    public function getPartnerCode(): string
    {
        return $this->partnerCode;
    }

    public function getPointCode(): string
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
    public function toArray(): array
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
