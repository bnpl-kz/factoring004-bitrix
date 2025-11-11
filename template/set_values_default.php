<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let selectElement = document.getElementById('ACTION_FILE');

        setDefaultValue(selectElement)

        selectElement.addEventListener('change',function (e) {
            setDefaultValue(e.target)
        })

        function setDefaultValue(elem) {
            let description = document.querySelector('iframe').contentWindow.document;
            if (elem.value === 'bnplpayment') {
                document.querySelector("[name='CODE']").readOnly = true
                document.querySelector("[name='CODE']").value = 'factoring004'
                description.body.innerText = '<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_DESCRIPTION"); ?>';
            }
        }
    })
</script>
