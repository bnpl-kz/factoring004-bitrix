<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let selectElement = document.getElementById('ACTION_FILE');

        setDefaultValue(selectElement)

        selectElement.addEventListener('change',function (e) {
            setDefaultValue(e.target)
        })

        function setDefaultValue(elem) {
            let description = document.querySelector('iframe').contentWindow.document;
            if (elem.value === 'bnplpad') {
                document.querySelector("[name='CODE']").readOnly = true
                document.querySelector("[name='CODE']").value = 'factoring004_pad'
                description.body.innerText = '<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_DESCRIPTION"); ?>';
            } else {
                document.querySelector("[name='CODE']").readOnly = false
                document.querySelector("[name='CODE']").value = ''
                description.body.innerText = '';
            }
        }
    })
</script>
