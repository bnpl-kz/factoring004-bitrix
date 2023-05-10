<?php

namespace Bnpl\Payment;

use Bitrix\Main\HttpRequest;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
use Bitrix\Sale\Internals\BusinessValuePersonDomainTable;
use Bitrix\Sale\Internals\PaySystemActionTable;
use Bitrix\Sale\Order;
use CCurrencyLang;
use CJSCore;
use CUtil;

class EventHandler
{
    const MIN_SUM = 6000;
    const MAX_SUM = 200000;
    const REQUIRED_OPTIONS = [
        'BNPL_PAYMENT_API_OAUTH_LOGIN',
        'BNPL_PAYMENT_API_OAUTH_PASSWORD',
        'BNPL_PAYMENT_API_HOST',
        'BNPL_PAYMENT_PARTNER_NAME',
        'BNPL_PAYMENT_PARTNER_CODE',
        'BNPL_PAYMENT_POINT_CODE',
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
        if (!Config::getPaySystemId()) {
            return;
        }

        if (!static::isRequiredOptionsFilled()) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
            return;
        }

        if (!static::isIndividualPersonType($arUserResult['PERSON_TYPE_ID'])) {
            static::disablePaymentSystemIfEnabled($arPaySystemServiceAll);
            return;
        }

        static::addScheduleOrDisablePaymentMethod();

        if (Config::get('BNPL_PAYMENT_FILE')) {
            static::addJS();
        }
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

        return true;
    }

    private static function addJS()
    {
        $paymentId = static::getPaySystemId();
        if ($paymentId === null) {
            return false;
        }

        $agreementLink = static::getAgreementLink();
        $agreementText = Loc::getMessage('BNPL_PAYMENT_AGREEMENT_TEXT');
        $agreementTextLink =  Loc::getMessage('BNPL_PAYMENT_AGREEMENT_TEXT_LINK');
        $agreementTextError =  Loc::getMessage('BNPL_PAYMENT_AGREEMENT_TEXT_ERROR');
        $agreementTextButton =  Loc::getMessage('BNPL_PAYMENT_AGREEMENT_TEXT_BUTTON');

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
                                    $('#bnpl-payment-offer-block').after("<button disabled class='btn btn-primary btn-lg mt-2 mb-2' id='bnpl-form-button' type='button'>$agreementTextButton</button>")
                                }
                                if (!$('#bnpl-error').length) {
                                    $('#bnpl-payment-offer-block').after('<p id="bnpl-error" class="text-danger">$agreementTextError</p>')
                                }
                            }
                        }
                        
                        function addElem() {
                            $('.checkbox').after("<div id='bnpl-payment-offer-block' class='mt-2 bnpl-payment-offer-block'><label class='form-check-label' for='bnpl_payment'><input class='mr-1' name='bnpl-payment-offer' id='bnpl_payment' type='checkbox'/>$agreementText <a href='$agreementLink' target='_blank'>$agreementTextLink</a></label></div>")
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
        if (!$id) {
            return '';
        }
        global $DB;
        $dbOption = $DB->Query("SELECT SUBDIR, FILE_NAME FROM b_file WHERE ID=$id");
        $result = $dbOption->Fetch();
        return '/upload/'.$result['SUBDIR'].'/'.$result['FILE_NAME'];
    }

    private static function addScheduleOrDisablePaymentMethod()
    {
        $paySystemId = Config::getPaySystemId();

        CJSCore::Init(['currency']);

        $currency = ['CURRENCY' => 'KZT', 'FORMAT' => CCurrencyLang::GetFormatDescription('KZT')];
        $currenciesJs = CUtil::PhpToJSObject([$currency], false, true, true);

        $minAmount = static::MIN_SUM;
        $maxAmount = static::MAX_SUM;
        $minAmountMessage = Loc::getMessage('BNPL_PAYMENT_MIN_AMOUNT_CONDITION');
        $maxAmountMessage = Loc::getMessage('BNPL_PAYMENT_MAX_AMOUNT_CONDITION');

        Asset::getInstance()->addCss('/bitrix/css/factoring004/' . PaymentScheduleAsset::FILE_CSS);
        Asset::getInstance()->addString('<script src="/bitrix/js/factoring004/' . PaymentScheduleAsset::FILE_JS . '" defer></script>');
        Asset::getInstance()->addString(
            <<<JS
                <script>
                    BX.Currency.setCurrencies($currenciesJs);
                
                    document.addEventListener('DOMContentLoaded', () => {
                        const editActivePaySystemBlock = BX.Sale.OrderAjaxComponent.editActivePaySystemBlock;
                        
                        BX.Sale.OrderAjaxComponent.editActivePaySystemBlock = function (activeNodeMode) {
                          editActivePaySystemBlock.call(this, activeNodeMode);
                      
                          if (!activeNodeMode) return;
                          
                          const input = document.getElementById('ID_PAY_SYSTEM_ID_' + $paySystemId);
                          
                          if (!input.checked) return;
                          
                          const totalAmountSelector = '#bx-soa-total .bx-soa-cart-total-line-total .bx-soa-cart-d';
                          const totalAmountElem = document.querySelector(totalAmountSelector);
                          const totalAmount = Math.ceil(parseFloat(totalAmountElem.textContent.replace(/\s+/g, '')));
                          const container = this.paySystemBlockNode.querySelector('.bx-soa-section-content .bx-soa-pp');
                          
                          if (totalAmount < $minAmount || totalAmount > $maxAmount) {
                            disablePaymentMethod(container, input, totalAmount);
                            return;
                          }
                          
                          let elem = document.getElementById('factoring004-schedule');
                          
                          if (!elem) {
                            const schedule = new Factoring004.PaymentSchedule({
                              elemId: 'factoring004-schedule',
                              totalAmount,
                            });
                          
                            elem = document.createElement('div');
                            elem.id = 'factoring004-schedule';
                            
                            schedule.renderTo(elem);
                          }
                        
                          container.insertAdjacentElement('afterend', elem);
                        };
                        
                        function disablePaymentMethod (container, input, totalAmount) {
                            input.checked = false;
                            input.disabled = true;
                              
                            let template;
                            let amount;
                            let value;
                            
                            if (totalAmount < $minAmount) {
                              template = '$minAmountMessage';
                              amount = BX.Currency.currencyFormat($minAmount, 'KZT', true);
                              value = BX.Currency.currencyFormat($minAmount - totalAmount, 'KZT', true);
                            } else {
                              template = '$maxAmountMessage';
                              amount = BX.Currency.currencyFormat($maxAmount, 'KZT', true);
                              value = BX.Currency.currencyFormat(totalAmount - $maxAmount, 'KZT', true);
                            }
                            
                            const msg = template.replace('{amount}', amount).replace('{value}', value);
                            
                            container.insertAdjacentHTML('afterend', `<div style="color: red; margin: 1rem 0">\${msg}</div>`);
                        }
                    });
                </script>
JS
        );
    }
}
