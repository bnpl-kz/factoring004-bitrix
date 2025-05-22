<script>
    document.addEventListener('DOMContentLoaded',function (e) {
        let selectElementPad = document.getElementById('ACTION_FILE');

        setDefaultValuePad(selectElementPad)

        selectElementPad.addEventListener('change',function (e) {
            setDefaultValuePad(e.target)
        })

        function setDefaultValuePad(elem_pad) {
            let description_pad = document.querySelector('iframe').contentWindow.document;
            if (elem_pad.value === 'bnplpad') {
                document.querySelector("[name='CODE']").readOnly = true
                document.querySelector("[name='CODE']").value = 'factoring004_pad'
                description_pad.body.innerText = '<?= \Bitrix\Main\Localization\Loc::getMessage("BNPL_PAYMENT_PAD_DESCRIPTION"); ?>';
            }
        }
    })
</script>
