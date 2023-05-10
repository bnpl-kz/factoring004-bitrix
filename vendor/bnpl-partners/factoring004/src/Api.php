<?php

declare(strict_types=1);

namespace BnplPartners\Factoring004;

use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\PreApp\PreAppResource;
use BnplPartners\Factoring004\Transport\GuzzleTransport;
use BnplPartners\Factoring004\Transport\TransportInterface;
use OutOfBoundsException;

/**
 * @property-read \BnplPartners\Factoring004\PreApp\PreAppResource $preApps
 * @property-read \BnplPartners\Factoring004\Otp\OtpResource $otp
 * @property-read \BnplPartners\Factoring004\ChangeStatus\ChangeStatusResource $changeStatus
 */
class Api
{
    private PreAppResource $preApps;
    private OtpResource $otp;
    private ChangeStatusResource $changeStatus;

    public function __construct(
        string $baseUri,
        ?AuthenticationInterface $authentication = null,
        ?TransportInterface $transport = null
    ) {
        $transport = $transport ?? new GuzzleTransport();

        $this->preApps = new PreAppResource($transport, $baseUri, $authentication);
        $this->otp = new OtpResource($transport, $baseUri, $authentication);
        $this->changeStatus = new ChangeStatusResource($transport, $baseUri, $authentication);
    }

    public static function create(
        string $baseUri,
        ?AuthenticationInterface $authentication = null,
        ?TransportInterface $transport = null
    ): Api {
        return new self($baseUri, $authentication, $transport);
    }

    public function __get(string $name): AbstractResource
    {
        if ($name === 'preApps') {
            return $this->preApps;
        }

        if ($name === 'otp') {
            return $this->otp;
        }

        if ($name === 'changeStatus') {
            return $this->changeStatus;
        }

        throw new OutOfBoundsException("Property {$name} does not exist");
    }
}
