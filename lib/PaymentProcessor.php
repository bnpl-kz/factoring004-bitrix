<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;
use Bitrix\Sale\Order;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\PreApp\PreAppMessage;

class PaymentProcessor
{
    /**
     * @var \BnplPartners\Factoring004\Api
     */
    private $api;

    /**
     * @var \Bnpl\Payment\PreAppOrderManager
     */
    private $orderManager;

    public function __construct(Api $api, PreAppOrderManager $orderManager)
    {
        $this->api = $api;
        $this->orderManager = $orderManager;
    }

    /**
     * @return \Bitrix\Main\HttpResponse
     *
     * @throws \Exception
     */
    public function preApp(HttpRequest $request)
    {
        $order = Order::load($request->get('order_id'));

        if (!$order) {
            return $this->sendErrorResponse(404, 'Order not found');
        }

        if ($order->isPaid()) {
            return $this->sendErrorResponse(400, 'Order is paid');
        }

        if ($order->isCanceled()) {
            return $this->sendErrorResponse(400, 'Order is canceled');
        }

        $preApp = $this->api->preApps->preApp($this->createPreAppMessage($order, $this->extractServerHost($request)));

        $this->orderManager->syncOrder($order->getId(), $preApp->getPreAppId());

        return (new HttpResponse())
            ->setStatus(302)
            ->addHeader('Location', $preApp->getRedirectLink());
    }

    /**
     * @param int $status
     * @param string $message
     *
     * @return \Bitrix\Main\HttpResponse
     * @throws \Bitrix\Main\ArgumentNullException
     * @throws \Bitrix\Main\ArgumentTypeException
     */
    private function sendErrorResponse($status, $message)
    {
        return (new HttpResponse())
            ->addHeader('Content-Type', 'application/json')
            ->setStatus($status)
            ->setContent(json_encode(compact('message')));
    }

    /**
     * @param string $serverHost
     *
     * @return \BnplPartners\Factoring004\PreApp\PreAppMessage
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\NotImplementedException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private function createPreAppMessage(Order $order, $serverHost)
    {
        $paymentCollection = $order->getPropertyCollection();
        $phone = $paymentCollection->getPhone();
        $city = $paymentCollection->getItemByOrderPropertyCode('CITY');
        $itemsQuantity = array_map(function ($item) {
            return (int) $item->getField('QUANTITY');
        }, $order->getBasket()->getBasketItems());

        return PreAppMessage::createFromArray([
            'partnerData' => [
                'partnerName' => Config::get('BNPL_PAYMENT_PARTNER_NAME'),
                'partnerCode' => Config::get('BNPL_PAYMENT_PARTNER_CODE'),
                'pointCode' => Config::get('BNPL_PAYMENT_POINT_CODE'),
                'partnerEmail' => Config::get('BNPL_PAYMENT_PARTNER_EMAIL'),
                'partnerWebsite' => Config::get('BNPL_PAYMENT_PARTNER_WEBSITE'),
            ],
            'billNumber' => (string) $order->getId(),
            'billAmount' => (int) round($order->getPrice()),
            'itemsQuantity' => array_sum($itemsQuantity),
            'successRedirect' => $serverHost,
            'postLink' => $serverHost . $this->resolvePostLink(),
            'phoneNumber' => $phone ? $phone->getValue() : null,
            'deliveryPoint' => [
                'city' => $city ? $city->getValue() : '',
            ],
            'items' => array_map(function ($item) {
                return [
                    'itemId' => (string) $item->getProductId(),
                    'itemName' => $item->getField('NAME'),
                    'itemCategory' => (string) $item->getField('PRICE_TYPE_ID'),
                    'itemQuantity' => (int) $item->getField('QUANTITY'),
                    'itemPrice' => (int) round($item->getField('PRICE')),
                    'itemSum' => (int) round($item->getField('PRICE')),
                ];
            }, $order->getBasket()->getBasketItems()),
        ]);
    }

    /**
     * @return string
     */
    private function extractServerHost(HttpRequest $request)
    {
        return $request->getServer()->getRequestScheme() . '://' . $request->getHttpHost();
    }

    /**
     * @return string
     */
    private function resolvePostLink()
    {
        $value = Config::get('BNPL_PAYMENT_POST_LINK');

        return $value ?: '/bitrix/tools/sale_ps_result.php?ps=bnpl.payment';
    }
}
