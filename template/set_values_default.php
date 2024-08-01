<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let selectElement = document.getElementById('ACTION_FILE');

        setDefaultValue(selectElement)

        selectElement.addEventListener('change',function (e) {
            setDefaultValue(e.target)
        })

        function setDefaultValue(elem) {
            let descriptionEditor = document.querySelector('iframe').contentWindow.document;
            let description = document.querySelector("input[name='DESCRIPTION']");
            if (elem.value === 'bnplpayment') {
                document.querySelector("[name='CODE']").readOnly = true
                document.querySelector("[name='CODE']").value = 'factoring004'
                if (description.value.length == 0) {
                    descriptionEditor.body.innerText = '<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_DESCRIPTION"); ?>';
                    description.value = '<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_DESCRIPTION"); ?>';
                }
            } else {
                document.querySelector("[name='CODE']").readOnly = false
                document.querySelector("[name='CODE']").value = ''
                id = document.querySelector("input[name='ID']")
                if (id.value == 0) {
                    descriptionEditor.body.innerText = '';
                    description.value = '';
                }
            }
        }
    })
</script>
