<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Page\Asset;
use Bitrix\Sale\BusinessValue;
use Bitrix\Sale\Internals\BusinessValuePersonDomainTable;
use Bitrix\Sale\Internals\PaySystemActionTable;
use Bitrix\Sale\Order;

class EventHandler
{
    const MIN_SUM = 6000;
    const MAX_SUM = 200000;
    const REQUIRED_OPTIONS = [
        'BNPL_PAYMENT_API_OAUTH_PREAPP_TOKEN',
        'BNPL_PAYMENT_API_OAUTH_ACCOUNTING_SERVICE_TOKEN',
        'BNPL_PAYMENT_API_HOST',
        'BNPL_PAYMENT_PARTNER_NAME',
        'BNPL_PAYMENT_PARTNER_CODE',
        'BNPL_PAYMENT_POINT_CODE',
        'BNPL_PAYMENT_PARTNER_EMAIL',
        'BNPL_PAYMENT_PARTNER_WEBSITE',
        'BNPL_PAYMENT_FILE'
    ];

    public static function hidePaySystem(
        Order $order,
        array &$arUserResult,
        HttpRequest $request,
        array &$arParams,
        array &$arResult,
        array &$arDeliveryServiceAll,
        &$arPaySystemServiceAll
    ) {
        if (!static::isRequiredOptionsFilled()) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
            return;
        }

        if (!static::isIndividualPersonType($arUserResult['PERSON_TYPE_ID'])) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
        }

        if ($order->getPrice() < static::MIN_SUM || $order->getPrice() > static::MAX_SUM) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
        }
        static::addJS();

    }

    private static function getPaymentSystemIndex(array $paymentSystems)
    {
        foreach ($paymentSystems as $i => $item) {
            if ($item['CODE'] === 'factoring004') {
                return $i;
            }
        }
        return null;
    }

    private static function isIndividualPersonType($personTypeId)
    {
        return (bool) BusinessValuePersonDomainTable::getCount([
            'PERSON_TYPE_ID' => $personTypeId,
            'DOMAIN' => 'I',
        ]);
    }

    private static function disablePaymentSystemIfEnabled(array &$paymentSystems)
    {
        $index = static::getPaymentSystemIndex($paymentSystems);

        if ($index) {
            unset($paymentSystems[$index]);
        }
    }

    /**
     * @return bool
     */
    private static function isRequiredOptionsFilled()
    {
        foreach (static::REQUIRED_OPTIONS as $option) {
            if (!Config::get($option)) {
                return false;
            }
        }

        return !empty(Config::getDeliveryIds());
    }

    private static function addJS()
    {
        $paymentId = static::getPaySystemId();
        if ($paymentId === null) {
            return false;
        }

        $agreementLink = static::getAgreementLink();

        Asset::getInstance()->addString(
            <<<JS
                <script>
                    $(document).ready(function() {
                        toggleAgreementCheckbox()
                        if ($('#bnpl_payment').length) toggleSubmitButton($('#bnpl_payment'))
                        
                        $(document).on('click',function(e) {
                            if (e.target.id == 'bnpl_payment' || e.target.htmlFor == 'bnpl_payment') return
                            toggleAgreementCheckbox()
                        })
                        
                        $(document).on('change','#bnpl_payment',function(e) {
                            toggleSubmitButton(e.target)
                        })
                        
                        function drawCheckbox() {
                            removeElem()
                            addElem()
                        }
                        
                        function removeCheckbox() {
                             removeElem()
                        }
                        
                        function toggleAgreementCheckbox()
                        {
                            let payValue = $('input[name="PAY_SYSTEM_ID"]:checked:enabled').val();
                            if (payValue == '$paymentId') {
                                if ($('#bnpl-payment-offer-block').length) return
                                drawCheckbox()
                                toggleSubmitButton($('#bnpl_payment'))
                            } else {
                                removeCheckbox()
                                toggleSubmitButton(null)
                            }
                        }
                        
                        function toggleSubmitButton(elem) {
                            if (!elem || elem.checked) {
                                $('#bnpl-form-button').remove()
                                $('a[data-save-button]').prop('style','margin: 10px 0')
                                $('#bnpl-error').remove()
                            } else {
                                $('a[data-save-button]').prop('style','display: none !important')
                                if (!$('#bnpl-form-button').length) {
                                    $('#bnpl-payment-offer-block').after("<button disabled class='btn btn-primary btn-lg mt-2 mb-2' id='bnpl-form-button' type='button'>Оформить заказ</button>")
                                }
                                if (!$('#bnpl-error').length) {
                                    $('#bnpl-payment-offer-block').after('<p id="bnpl-error" class="text-danger">Вам нужно согласиться с условиями</p>')
                                }
                            }
                        }
                        
                        function addElem() {
                            $('.checkbox').after("<div id='bnpl-payment-offer-block' class='mt-2 bnpl-payment-offer-block'><label class='form-check-label' for='bnpl_payment'><input class='mr-1' name='bnpl-payment-offer' id='bnpl_payment' type='checkbox'/>Я согласен <a href='$agreementLink' target='_blank'>с условиями платежной системы Рассрочка 0-0-4</a></label></div>")
                        }
                        
                        function removeElem() {
                            $('#bnpl-payment-offer-block').remove()
                        }
                        
                    })
                </script>
JS
        );
    }


    private static function getPaySystemId()
    {
        return PaySystemActionTable::getRow(array(
            'filter'=>array('CODE'=>'factoring004')
        ))['ID'];
    }

    private static function getAgreementLink()
    {
        $id = Config::get('BNPL_PAYMENT_FILE');
        global $DB;
        $dbOption = $DB->Query("SELECT SUBDIR, FILE_NAME FROM b_file WHERE ID=$id");
        $result = $dbOption->Fetch();
        return '/upload/'.$result['SUBDIR'].'/'.$result['FILE_NAME'];
    }

}
