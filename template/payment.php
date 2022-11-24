<?php

use Bitrix\Main\Localization\Loc;
use Bnpl\Payment\Config;

$action = $params['PAYMENT_ACTION'];
$orderId = $params['ORDER_ID'];
$url = Config::get('BNPL_PAYMENT_API_HOST');

$domain = stripos($url, 'dev') ? 'dev.bnpl.kz' : 'bnpl.kz';

?>

<form id="form-bnplpayment" action="<?= $action ?>" method="post">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="order_id" value="<?= $orderId ?>">
    <button id="button-bnplpayment" class="btn btn-primary"><?= Loc::getMessage('BNPL_PAYMENT_PAY_BUTTON') ?></button>
</form>

<?php

if (Config::get('BNPL_PAYMENT_CLIENT_ROUTE') === 'modal') {
    echo "<div id='modal-bnplpayment'></div>
            <script defer src='https://$domain/widget/index_bundle.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    let bnplpaymentForm = $('#form-bnplpayment')
                    let bnplpaymentButton = $('#button-bnplpayment');
                    let formData = new FormData()
                        bnplpaymentForm.submit(function (e) {
                          e.preventDefault();
                          formData.append('order_id', '$orderId')
                          formData.append('sessid', $('#sessid').val())
                          $.ajax({
                            url: '$action',
                            type: 'post',
                            data: formData,
                            processData: false,
                            enctype: 'multipart/form-data',
                            contentType: false,
                            success: function (res) {
                                if (res.redirectErrorPage) {
                                    return window.location.replace(res.redirectErrorPage)
                                }
                                const bnplKzApi = new BnplKzApi.CPO(
                                {
                                  rootId: 'modal-bnplpayment',
                                  callbacks: {
                                    onLoad: () => bnplpaymentButton.attr('disabled', true),
                                    onError: () => window.location.replace(res.redirectLink),
                                    onClosed: () => bnplpaymentButton.attr('disabled', false),
                                    onEnd: () => window.location.replace('/')
                                  }
                                });
                                bnplKzApi.render({
                                    redirectLink: res.redirectLink
                                });
                            },
                            error: function () {
                                window.location.href = window.location.replace('/')
                            }
                          });
                    })
                });
            </script>
            ";
}
?>