<?php
    \Bitrix\Main\UI\Extension::load("ui.notification");
?>
<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let title = document.querySelector('.adm-detail-title-view-tab')

        let form = '<div style="text-align: center" class="cache-clear-button-block"> <p class="cache-clear-help"><?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_CACHE_HELP"); ?></p> <button class="cache-clear-button" type="button"><?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_CACHE_BUTTON"); ?></button></div>'

        title.insertAdjacentHTML("afterend", form)

        let button = document.querySelector('.cache-clear-button');

        button.addEventListener('click', function () {
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
    .cache-clear-button {
        padding: 5px 10px;
        background-color: #b03c3c;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        border: none;
    }
    .cache-clear-button:hover {
        background-color: #c25454;
    }
    .cache-clear-help {
        font-weight: bold;
        font-style: italic;
    }
</style>