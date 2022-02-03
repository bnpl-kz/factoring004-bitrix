<?php

use Bitrix\Main\Localization\Loc;
?>

<form id="form-bnplpayment" action="<?= $params['PAYMENT_ACTION']?>" method="post">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="order_id" value="<?= $params['ORDER_ID']?>">
    <button class="btn btn-primary"><?= Loc::getMessage('BNPL_PAYMENT_PAY_BUTTON') ?></button>
</form>
