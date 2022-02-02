<?php

namespace Sale\Handlers\PaySystem;

use Bitrix\Main;
use Bitrix\Main\Error;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\Logger;
use Bitrix\Sale\PaySystem\ServiceResult;

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
     * @return string
     */
    public function getPaymentIdFromRequest(Request $request)
    {
        return $request->get('billNumber');
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

        $status = $request->get('status');

        if ($status === 'completed') {
            $result->setOperationType(ServiceResult::MONEY_COMING);
            $psStatus = 'Y';
        } else {
            $result->setOperationType(ServiceResult::MONEY_LEAVING);
            $psStatus = 'N';
        }

        $result->setPsData([
            'PS_STATUS' => $psStatus,
            'PS_STATUS_CODE' => $status,
            'PS_SUM' => $payment->getSum(),
            'PS_CURRENCY' => $payment->getOrder()->getCurrency(),
            'PS_RESPONSE_DATE' => new DateTime(),
        ]);

        return $result;
    }

    /**
     * @return string[]
     */
    public function getCurrencyList()
    {
        return ['RUB', 'KZT'];
    }
}
