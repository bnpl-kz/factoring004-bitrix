<?php
use Bitrix\Sale\PaySystem\Manager;

\Bitrix\Main\UI\Extension::load("ui.notification");

$paySystemId = isset($_REQUEST['ID']) ? (int)$_REQUEST['ID'] : 0;
if (!$paySystemId) {
    return;
}

$paySystem = Manager::getById($paySystemId);
if (!$paySystem || empty($paySystem['ACTION_FILE']) || $paySystem['ACTION_FILE'] !== 'bnplpad') {
    return;
}
?>
<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let title_pad = document.querySelector('.adm-detail-title-view-tab')

        let form_pad = '<div style="text-align: center" class="cache-clear-button-block-pad"> <p class="cache-clear-help-pad"><?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_CACHE_HELP"); ?></p> <button class="cache-clear-button-pad" type="button"><?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_CACHE_BUTTON"); ?></button></div>'

        title_pad.insertAdjacentHTML("afterend", form_pad)

        let button_pad = document.querySelector('.cache-clear-button-pad');

        button_pad.addEventListener('click', function () {
            fetch('/bitrix/admin/bnplpad_cache_clear.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).then((response) => response.json()).then((data) => {
                if (data.success) {
                    BX.UI.Notification.Center.notify({
                        content: "<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_CACHE_ALERT"); ?>",
                        position: "bottom-right"
                    });
                }
            })
        })
    })
</script>

<style>
    .cache-clear-button-pad {
        padding: 5px 10px;
        background-color: #b03c3c;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        border: none;
    }
    .cache-clear-button-pad:hover {
        background-color: #c25454;
    }
    .cache-clear-help-pad {
        font-weight: bold;
        font-style: italic;
    }
</style>