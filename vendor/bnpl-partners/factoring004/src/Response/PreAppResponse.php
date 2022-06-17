<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004\Response;

use BnplPartners\Factoring004\ArrayInterface;
use BnplPartners\Factoring004\PreApp\Status;
use JsonSerializable;

/**
 * @psalm-immutable
 */
class PreAppResponse implements JsonSerializable, ArrayInterface
{
    /**
     * @var \BnplPartners\Factoring004\PreApp\Status
     */
    private $status;
    /**
     * @var string
     */
    private $preAppId;
    /**
     * @var string
     */
    private $redirectLink;

    public function __construct(Status $status, string $preAppId, string $redirectLink)
    {
        $this->status = $status;
        $this->preAppId = $preAppId;
        $this->redirectLink = $redirectLink;
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, preappId: string, redirectLink: string} $data
     */
    public static function createFromArray($data): PreAppResponse
    {
        return new self(new Status($data['status']), $data['preappId'], $data['redirectLink']);
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function getPreAppId(): string
    {
        return $this->preAppId;
    }

    public function getRedirectLink(): string
    {
        return $this->redirectLink;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{status: string, preappId: string, redirectLink: string}
     */
    public function toArray(): array
    {
        return [
            'status' => (string) $this->getStatus()->getValue(),
            'preappId' => $this->getPreAppId(),
            'redirectLink' => $this->getRedirectLink(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
