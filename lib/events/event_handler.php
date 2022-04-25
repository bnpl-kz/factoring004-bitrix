<?php

namespace Bnpl\Payment;

use \Bitrix\Main\Loader;
use CSalePaySystemAction;

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
        'BNPL_PAYMENT_PARTNER_WEBSITE'
    ];



    public static function validateBnplOrder(&$arFields)
    {
        $paymentSystemId = $arFields['PAY_SYSTEM_ID'];

        if (self::isBnplPayment($paymentSystemId)) {
            $bnplOfferCheck = isset($_POST['bnpl-payment-offer']) ? $_POST['bnpl-payment-offer'] : false;
            if (!$bnplOfferCheck) {
                global $APPLICATION;
                $text = GetMessage('BNPL_PAYMENT_AGREEMENT_TEXT_ERROR');
                $APPLICATION->ThrowException($text);
                return false;
            }
        }
    }


    public static function hidePaySystem(
        &$arResult,
        &$arUserResult,
        $arParams
    ) {
        // echo '<pre>';
        // print_r($arResult);
        // echo '</pre>';
        $bnplPS = self::getPaymentSystem($arResult['PAY_SYSTEM']);
        $bnplParams = unserialize($bnplPS['PSA_PARAMS']);
        if (!static::isRequiredOptionsFilled($bnplParams)) {
            static::disablePaymentSystemIfEnabled($arResult['PAY_SYSTEM'], $bnplPS['ID']);
            return;
        }

        if (!static::isIndividualPersonType($arUserResult['PERSON_TYPE_ID'])) {
            static::disablePaymentSystemIfEnabled($arResult['PAY_SYSTEM'], $bnplPS['ID']);
        }

        if ($arResult['ORDER_PRICE'] < static::MIN_SUM || $arResult['ORDER_PRICE'] > static::MAX_SUM) {
            static::disablePaymentSystemIfEnabled($arResult['PAY_SYSTEM'], $bnplPS['ID']);
        }

        if ($bnplParams['BNPL_PAYMENT_FILE']) {
            static::addJS($bnplPS['ID'], $bnplParams['BNPL_PAYMENT_FILE']);
        }
    }

    private static function getPaymentSystem(array $paymentSystems)
    {
        foreach ($paymentSystems as $i => $item) {
            if (self::isBnplPaymentByAction($item['PSA_ACTION_FILE'])) {
                return $item;
            }
        }
        return null;
    }

    private static function isIndividualPersonType($personTypeId)
    {
        return (bool) $personTypeId == 1;
    }

    private static function disablePaymentSystemIfEnabled(array &$paymentSystems, $paySystemId)
    {
        unset($paymentSystems[$paySystemId]);
    }

    /**
     * @return bool
     */
    private static function isRequiredOptionsFilled($paymentParams)
    {
        foreach (static::REQUIRED_OPTIONS as $option) {
            if (!array_key_exists($option, $paymentParams)) {
                return false;
            }
        }

        return true;
    }

    private static function isBnplPayment($paymentId)
    {   
        $action = new CSalePaySystemAction();
        $payment = $action->GetList(
            false,
            ['PAY_SYSTEM_ID' => $paymentId],
            false,
            false,
            ['ACTION_FILE']
        )->Fetch();
        if ($payment) {
            return self::isBnplPaymentByAction($payment['ACTION_FILE']);
        } else {
            return false;
        }
    }

    private static function isBnplPaymentByAction($paymentAction)
    {
        $actionParts = explode('/', $paymentAction);
        $actionIndex = count($actionParts)-1;
        return $actionParts[$actionIndex] == 'bnplpayment';
    }

    private static function addJS($paymentId, $bnplAgreementFileId)
    {

        IncludeModuleLangFile(__FILE__);

        $agreementLink = static::getAgreementLink($bnplAgreementFileId['VALUE']);
        $agreementText = GetMessage('BNPL_PAYMENT_AGREEMENT_TEXT');
        $agreementTextLink =  GetMessage('BNPL_PAYMENT_AGREEMENT_TEXT_LINK');
        $agreementTextError =  GetMessage('BNPL_PAYMENT_AGREEMENT_TEXT_ERROR');
        $agreementTextButton =  GetMessage('BNPL_PAYMENT_AGREEMENT_TEXT_BUTTON');

        echo <<<JS
                <script id="bnpl-script">
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
                            console.log(payValue);
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
                                if (!$('#bnpl-error').length) {
                                    $('#bnpl-payment-offer-block').after('<p id="bnpl-error" class="text-danger"> $agreementTextError</p>')
                                }
                            }
                        }
                        
                        function addElem() {
                            formLast = $("form[action='/personal/order/make/']").children("input").slice(-2);
                            console.log(formLast);
                            $(formLast[0]).after("<div id='bnpl-payment-offer-block' class='mt-2 bnpl-payment-offer-block'><label class='form-check-label' for='bnpl_payment'><input class='mr-1' name='bnpl-payment-offer' id='bnpl_payment' type='checkbox'/> $agreementText <a href='$agreementLink' target='_blank'> $agreementTextLink</a></label></div>")
                        }
                        
                        function removeElem() {
                            $('#bnpl-payment-offer-block').remove()
                        }
                        
                    })
                </script>
JS;
    }

    private static function getAgreementLink($id)
    {
        global $DB;
        $dbOption = $DB->Query("SELECT SUBDIR, FILE_NAME FROM b_file WHERE ID=$id");
        $result = $dbOption->Fetch();
        return '/upload/'.$result['SUBDIR'].'/'.$result['FILE_NAME'];
    }

}
