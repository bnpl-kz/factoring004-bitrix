<?php

declare(strict_types=1);

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\HttpResponse;
use Bitrix\Main\Response;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Order;
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\PreApp\PreAppMessage;

class PaymentProcessor
{
    private Api $api;
    private P2reAppOrderManager $orderManager;

    public function __construct(Api $api, PreAppOrderManager $orderManager)
    {
        $this->api = $api;
        $this->orderManager = $orderManager;
    }

    /**
     * @throws \Exception
     */
    public function preApp(HttpRequest $request): Response
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

    private function sendErrorResponse(int $status, string $message): Response
    {
        return (new HttpResponse())
            ->addHeader('Content-Type', 'application/json')
            ->setStatus($status)
            ->setContent(json_encode(compact('message')));
    }

    private function createPreAppMessage(Order $order, string $serverHost): PreAppMessage
    {
        return PreAppMessage::createFromArray([
            'partnerData' => [
                'partnerName' => BusinessValue::getValuesByCode('bnpl.payment', 'BNPL_PAYMENT_PARTNER_NAME')[0],
                'partnerCode' => BusinessValue::getValuesByCode('bnpl.payment', 'BNPL_PAYMENT_PARTNER_CODE')[0],
                'pointCode' => BusinessValue::getValuesByCode('bnpl.payment', 'BNPL_PAYMENT_POINT_CODE')[0],
            ],
            'billNumber' => (string) $order->getId(),
            'billAmount' => (int) round($order->getPrice()),
            'itemsQuantity' => $order->getBasket()->count(),
            'successRedirect' => $serverHost,
            'postLink' => $serverHost . '/bitrix/tools/sale_ps_result.php?ps=bnpl.payment',
        ]);
    }

    private function extractServerHost(HttpRequest $request): string
    {
        return $request->getServer()->getRequestScheme() . '://' . $request->getHttpHost();
    }
}
