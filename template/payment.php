<?php

use Bitrix\Main\Localization\Loc;
use Bnpl\Payment\Config;

$action = $params['PAYMENT_ACTION'];
$orderId = $params['ORDER_ID'];
$url = Config::get('BNPL_PAYMENT_API_HOST');

$domain = stripos($url, 'dev') ? 'dev.bnpl.kz' : 'bnpl.kz';

?>
    <style>
        #button-bnplpayment {
            border: none;
            cursor: pointer;
            padding: 16px 28px;
            border-radius: 16px;
            background: linear-gradient(
                    89.93deg,
                    #fc96fc 0.08%,
                    #959ef1 46.14%,
                    #0dc9d5 99.97%
            );
            box-shadow: 0px 3px 15px 0px rgba(255, 255, 255, 0.64) inset;
            box-shadow: -2px -6px 15px 0px rgba(0, 0, 0, 0.15) inset;
        }

        #button-bnplpayment > p {
            font-family: "Roboto", sans-serif;
            font-size: 25px;
            font-weight: 700;
            line-height: 20px;
            margin: 0;
            color: #fff;
        }

        #button-bnplpayment > span {
            font-family: "Roboto", sans-serif;
            font-size: 22px;
            font-weight: 500;
            line-height: 20px;
            margin: 0;
            color: #fff;
        }
    </style>
<form id="form-bnplpayment" action="<?= $action ?>" method="post">
    <input type="hidden" name="lang" value="<?=LANGUAGE_ID?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="order_id" value="<?= $orderId ?>">
    <button class="bnplButton" id="button-bnplpayment">
        <p>Купить в рассрочку</p>
    </button>
</form>

<?php

if (Config::get('BNPL_PAYMENT_CLIENT_ROUTE') === 'modal') {
    echo "<div id='modal-bnplpayment'></div>
            <script defer src='https://$domain/widget/index_bundle.js'></script>
            <script>
            setTimeout((function () {
                let bnplpaymentForm = document.getElementById('form-bnplpayment')
                let bnplpaymentButton = document.getElementById('button-bnplpayment');
                let sessId = document.getElementById('sessid')
                let formData = new FormData()
                bnplpaymentForm.addEventListener('submit', function (e) {
                          e.preventDefault();
                          formData.append('order_id', '$orderId')
                          formData.append('sessid', sessId.value)
                          fetch('$action',{
                              method: 'POST',
                              headers: {
                                  'X-Requested-With': 'XMLHttpRequest'
                              },
                              body: formData,
                          })
                          .then(response => response.json())
                          .then((result) => {
                                if (result.redirectErrorPage) {
                                    return window.location.replace(result.redirectErrorPage)
                                }
                                const bnplKzApi = new BnplKzApi.CPO(
                                {
                                  rootId: 'modal-bnplpayment',
                                  callbacks: {
                                    onLoad: () => bnplpaymentButton.setAttribute('disabled', true),
                                    onError: () => window.location.replace(result.redirectLink),
                                    onClosed: () => bnplpaymentButton.setAttribute('disabled', false),
                                    onDeclined: () => window.location.replace('/'),
                                    onEnd: () => window.location.replace('/')
                                  }
                                });
                                bnplKzApi.render({
                                    redirectLink: result.redirectLink
                                });
                          })
                          .catch((err) => {
                              window.location.href = window.location.replace('/');
                          })
                    })
            }))
            </script>
            ";
}
?>