<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use Bitrix\Sale\ResultError;
use Bnpl\Payment\PostlinkProcessor;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * @package Sale\Handlers\PaySystem
 */
class BnplPaymentHandler extends PaySystem\ServiceHandler
{
    /**
     * @param Payment $payment
     * @param Request|null $request
     *
     * @return PaySystem\ServiceResult
     *
     * @throws Main\ArgumentException
     * @throws Main\ArgumentOutOfRangeException
     * @throws Main\NotImplementedException
     */
    public function initiatePay(Payment $payment, Request $request = null)
    {
        $busValues = $this->getParamsBusValue($payment);

        $this->setExtraParams($busValues + [
            'PAYMENT_ACTION' => $this->getUrl($payment, 'pay'),
            'ORDER_ID' => $payment->getOrderId(),
        ]);

        return $this->showTemplate($payment, "payment");
    }

    /**
     * @return string[]
     */
    public static function getIndicativeFields()
    {
        return ['ps' => 'bnpl.payment'];
    }

    /**
     * @param Request $request
     *
     * @return int|null
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        $payment = $this->getPaymentFromRequest($request);

        if (!is_null($payment)) {
            return $payment->getId();
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getUrlList()
    {
        return [
            'pay' => [
                self::ACTIVE_URL => '/personal/order/payment/bnplpayment.php',
            ],
        ];
    }

    /**
     * @param Payment $payment
     * @param Request $request
     *
     * @return PaySystem\ServiceResult
     */
    public function processRequest(Payment $payment, Request $request)
    {
        $postlinkProccesor = new PostlinkProcessor();

        return $postlinkProccesor->processRequest($payment, $request);
    }

    /**
     * @return string[]
     */
    public function getCurrencyList()
    {
        return ['RUB', 'KZT'];
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     */
    public function sendResponse(ServiceResult $result, Request $request)
    {
        $postlinkProcessor = new PostlinkProcessor();

        $postlinkProcessor->sendResponse($result, $request);
    }
}
