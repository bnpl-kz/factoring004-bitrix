<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\Logger;
use Bitrix\Sale\PaySystem\ServiceResult;

/**
 * @package Sale\Handlers\PaySystem
 */
class BnplPaymentHandler extends PaySystem\ServiceHandler
{
    const STATUS_PREAPPROVED = 'preapproved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

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
     * @return string|null
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        try {
            $data = Json::decode($this->readInputStream());
        } catch (Main\ArgumentException $e) {
            return null;
        }

        return isset($data['billNumber']) ? $data['billNumber'] : null;
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
        $result = new ServiceResult();

        if ($payment->isPaid()) {
            $error = 'Order has already payed';
            Logger::addError($error);
            return $result->addError(new Error($error));
        }

        if ($payment->getOrder()->isCanceled()) {
            $error = 'Order is canceled';
            Logger::addError($error);
            return $result->addError(new Error($error));
        }

        try {
            $data = Json::decode($this->readInputStream());
        } catch (Main\ArgumentException $e) {
            return $result;
        }

        $status = $data['status'];

        if ($status === static::STATUS_COMPLETED) {
            $result->setOperationType(ServiceResult::MONEY_COMING);
            $psStatus = 'Y';
        } elseif ($status === static::STATUS_DECLINED) {
            $result->setOperationType(ServiceResult::MONEY_LEAVING);
            $psStatus = 'N';
        } else {
            return $result;
        }

        $result->setPsData([
            'PS_STATUS' => $psStatus,
            'PS_STATUS_CODE' => $status,
            'PS_STATUS_DESCRIPTION' => 'Factoring004',
            'PS_STATUS_MESSAGE' => implode('; ', [
                'Status: ' . $status,
                'PreAppId: ' . $data['preappId'],
                'BillNumber: ' . $data['billNumber'],
            ]),
            'PS_SUM' => $payment->getSum(),
            'PS_CURRENCY' => $payment->getOrder()->getCurrency(),
            'PS_RESPONSE_DATE' => new DateTime(),
        ]);

        $order = $payment->getOrder();
        $order->setField('STATUS_ID', 'P');
        $order->save();

        return $result;
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
        $response = new Main\HttpResponse();
        $response->addHeader('Content-Type', 'application/json');

        try {
            $data = Json::decode($this->readInputStream());
        } catch (Main\ArgumentException $e) {
            $response->setStatus(400);
            $response->setContent(Json::encode(['error' => 'Invalid JSON']));
            $response->send();
            return;
        }

        $status = $data['status'];

        if ($status === static::STATUS_PREAPPROVED) {
            $response->setContent(Json::encode(['response' => static::STATUS_PREAPPROVED]));
        } elseif ($status === static::STATUS_COMPLETED) {
            $response->setContent(Json::encode(['response' => 'ok']));
        } else {
            $response->setContent(Json::encode(['response' => static::STATUS_DECLINED]));
        }

        $response->send();
    }

    /**
     * @return string
     */
    private function readInputStream()
    {
        return file_get_contents('php://input');
    }
}
