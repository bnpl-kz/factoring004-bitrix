<?php

namespace Bnpl\Payment;

use Bitrix\Main;
use Bitrix\Main\Request;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Order;
use Bitrix\Sale\Payment;
use Bitrix\Sale\PaySystem;
use Bitrix\Sale\PaySystem\ServiceResult;
use Bitrix\Sale\ResultError;
use Bnpl\Payment\Config;
use Bnpl\Payment\DebugLoggerFactory;
use BnplPartners\Factoring004\Exception\InvalidSignatureException;
use BnplPartners\Factoring004\Signature\PostLinkSignatureValidator;
use Exception;

class PostlinkProcessor
{
    const STATUS_PREAPPROVED = 'preapproved';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

    public function getPaymentFromRequest(Request $request)
    {
        try {
            $data = Json::decode($this->readInputStream());
        } catch (Main\ArgumentException $e) {
            return null;
        }

        if (empty($data['billNumber'])) {
            return null;
        }

        $order = Order::load($data['billNumber']);

        if (!$order) {
            throw new \Exception('Order not found');
        }

        $paymentCollection = $order->getPaymentCollection();

        foreach ($paymentCollection as $item) {
            if ($item->getPaymentSystemId() === (int) Config::getPaySystemId()) {
                return $item;
            }
        }

        return null;
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
            return $result;
        }

        if ($payment->getOrder()->isCanceled()) {
            return $result;
        }

        try {
            $data = Json::decode($this->readInputStream());
        } catch (Main\ArgumentException $e) {
            return $result;
        }

        DebugLoggerFactory::create()->createLogger()->debug('POSTLINK: ' . Json::encode($data));

        try {
            $this->validateSignature($data);
        } catch (InvalidSignatureException $e) {
            return $result->addError(new ResultError('Invalid signature', 'INVALID_SIGNATURE'));
        }

        $status = $data['status'];

        if ($status === static::STATUS_COMPLETED) {
            $result->setOperationType(ServiceResult::MONEY_COMING);
            $psStatus = 'Y';

            $order = $payment->getOrder();
            $order->setField('STATUS_ID', 'P');
            $order->save();
        } elseif ($status === static::STATUS_DECLINED) {
            $result->setOperationType(ServiceResult::MONEY_LEAVING);
            $psStatus = 'N';
        } else {
            return $result;
        }

        $payment->setFields([
            'PAID' => 'Y',
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
        $order->save();

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

        return $result;
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

        if (!$result->isSuccess()) {
            $response->setContent(Json::encode(['errors' => $result->getErrors()]));
            $response->setStatus(400);
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

    /**
     * @param array<string, mixed> $data
     *
     * @return void
     *
     * @throws \BnplPartners\Factoring004\Exception\InvalidSignatureException
     */
    private function validateSignature(array $data)
    {
        $secretKey = Config::get('BNPL_PAYMENT_PARTNER_CODE');
        $validator = new PostLinkSignatureValidator($secretKey);

        $validator->validateData($data);
    }
}