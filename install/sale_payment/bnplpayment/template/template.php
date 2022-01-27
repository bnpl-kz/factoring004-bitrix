<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    exit;
}

/**
 * @var array<string, mixed> $params
 */
?>
<form id="form-ztstpayment" action="<?=$params['PAYMENT_ACTION']?>" method="post">
    <input type="hidden" name="order_id" value="<?=$params['ORDER_ID']?>">
    <button class="btn btn-primary">Оплатить</button>
</form>