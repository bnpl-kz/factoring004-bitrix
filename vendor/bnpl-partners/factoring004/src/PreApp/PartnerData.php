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
    private string $partnerName;
    private string $partnerCode;
    private string $pointCode;
    private ?string $partnerEmail;
    private ?string $partnerWebsite;

    public function __construct(
        string $partnerName,
        string $partnerCode,
        string $pointCode,
        ?string $partnerEmail = null,
        ?string $partnerWebsite = null
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
    public static function createFromArray(array $partnerData): PartnerData
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
            $partnerData['partnerWebsite'] ?? null,
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

    public function getPartnerEmail(): ?string
    {
        return $this->partnerEmail;
    }

    public function getPartnerWebsite(): ?string
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
