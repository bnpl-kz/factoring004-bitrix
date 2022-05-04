<?php

IncludeModuleLangFile(__FILE__);

/**
 * @param $arParams
 * @param $arResult
 */
$ORDER_ID = $arResult['ORDER']['ID'];
$PAYMENT_ACTION = '/personal/order/payment/bnplpayment.php';
?>

<form id="form-bnplpayment" action="<?=$PAYMENT_ACTION ?>" method="post">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="order_id" value="<?=$ORDER_ID ?>">
    <button class="btn btn-primary"><?= GetMessage('BNPL_PAYMENT_PAY_BUTTON') ?></button>
</form>
