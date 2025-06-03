<?php

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

    /**
     * @param string $preAppId
     * @param string $redirectLink
     */
    public function __construct(Status $status, $preAppId, $redirectLink)
    {
        $this->status = $status;
        $this->preAppId = $preAppId;
        $this->redirectLink = $redirectLink;
    }

    /**
     * @param array<string, mixed> $data
     * @psalm-param array{status: string, preappId: string, redirectLink: string} $data
     * @return \BnplPartners\Factoring004\Response\PreAppResponse
     */
    public static function createFromArray(array $data)
    {
        return new self(new Status($data['status']), $data['preappId'], $data['redirectLink']);
    }

    /**
     * @return \BnplPartners\Factoring004\PreApp\Status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getPreAppId()
    {
        return $this->preAppId;
    }

    /**
     * @return string
     */
    public function getRedirectLink()
    {
        return $this->redirectLink;
    }

    /**
     * @return array<string, string>
     * @psalm-return array{status: string, preappId: string, redirectLink: string}
     */
    public function toArray()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
