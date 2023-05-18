<?php

namespace BnplPartners\Factoring004\Order;

use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\AuthenticationInterface;
use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\PreApp\PreAppMessage;
use BnplPartners\Factoring004\Response\PreAppResponse;
use InvalidArgumentException;

class OrderManager
{
    /**
     * @var \BnplPartners\Factoring004\Api
     */
    private $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /**
     * @param string $baseUri
     * @return \BnplPartners\Factoring004\Order\OrderManager
     * @param \BnplPartners\Factoring004\Auth\AuthenticationInterface|null $authentication
     */
    public static function create($baseUri, AuthenticationInterface $authentication = null)
    {
        return new self(Api::create($baseUri, $authentication));
    }

    /**
    * @param \BnplPartners\Factoring004\PreApp\PreAppMessage|array<string, mixed> $data
    * @psalm-param \BnplPartners\Factoring004\PreApp\PreAppMessage|array{
       partnerData: array{
       partnerName: string,
       partnerCode: string,
       pointCode: string,
       partnerEmail: string,
       partnerWebsite: string,
       },
       billNumber: string,
       billAmount: int,
       itemsQuantity: int,
       successRedirect: string,
       failRedirect?: string,
       postLink: string,
       phoneNumber?: string,
       expiresAt?: \DateTimeInterface,
       deliveryDate?: \DateTimeInterface,
       deliveryPoint?: array{
       region?: string,
       city?: string,
       district?: string,
       street?: string,
       house?: string,
       flat?: string,
       },
       items: array{
       itemId: string,
       itemName: string,
       itemCategory?: string,
       itemQuantity: int,
       itemPrice: int,
       itemSum: int,
       }[],
    * } $data
    *
    * @throws \InvalidArgumentException
    * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
    * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
    * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
    * @throws \BnplPartners\Factoring004\Exception\NetworkException
    * @throws \BnplPartners\Factoring004\Exception\TransportException
    * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
    * @throws \BnplPartners\Factoring004\Exception\ValidationException
    * @throws \BnplPartners\Factoring004\Exception\ApiException
     * @return \BnplPartners\Factoring004\Response\PreAppResponse
    */
    public function preApp($data)
    {
        if (is_array($data)) {
            $data = PreAppMessage::createFromArray($data);
        } elseif (!$data instanceof PreAppMessage) {
            throw new InvalidArgumentException('Data must be an instance of ' . PreAppMessage::class . ' or an array');
        }

        return $this->api->preApps->preApp($data);
    }

    /**
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationInterface
     */
    public function delivery($merchantId, $orderId, $amount)
    {
        return new Delivery($this->api->otp, $this->api->changeStatus, $merchantId, $orderId, $amount);
    }

    /**
     * @param string $merchantId
     * @param string $orderId
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationInterface
     */
    public function fullRefund($merchantId, $orderId)
    {
        return new FullRefund($this->api->otp, $this->api->changeStatus, $merchantId, $orderId);
    }

    /**
     * @param string $merchantId
     * @param string $orderId
     * @param int $amount
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationInterface
     */
    public function partialRefund($merchantId, $orderId, $amount)
    {
        return new PartialRefund($this->api->otp, $this->api->changeStatus, $merchantId, $orderId, $amount);
    }

    /**
     * @throws \BnplPartners\Factoring004\Exception\ErrorResponseException
     * @throws \BnplPartners\Factoring004\Exception\NetworkException
     * @throws \BnplPartners\Factoring004\Exception\DataSerializationException
     * @throws \BnplPartners\Factoring004\Exception\UnexpectedResponseException
     * @throws \BnplPartners\Factoring004\Exception\EndpointUnavailableException
     * @throws \BnplPartners\Factoring004\Exception\AuthenticationException
     * @throws \BnplPartners\Factoring004\Exception\TransportException
     * @param string $merchantId
     * @param string $orderId
     * @return \BnplPartners\Factoring004\Order\StatusConfirmationResponse
     */
    public function cancel($merchantId, $orderId)
    {
        return StatusConfirmationResponse::create(
            ChangeStatusCaller::create($this->api->changeStatus, $merchantId)
                ->call(new CancelOrder($orderId, CancelStatus::CANCEL()))
                ->getMsg()
        );
    }
}
