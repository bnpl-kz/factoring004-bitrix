<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateShipmentStatus = BX.Sale.Admin.OrderShipment.prototype.updateShipmentStatus;
        const orderId = '<?=$__order->getId()?>';
        let deliveryPopup = false;

        function showLoader () {
            const elem = document.createElement('div');
            elem.setAttribute('style', 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: #000; z-index: 9999; opacity: 0.5;');
            elem.id = 'bnpl_payment_delivery_send_overlay';

            document.body.appendChild(elem);
            BX.showWait();
        }

        function hideLoader () {
            let overlay = document.getElementById('bnpl_payment_delivery_send_overlay');
            if (overlay) {
                overlay.remove();
            }
            BX.closeWait();
        }

        function showError (message) {
            const error = document.createElement('p');
            error.id = 'bnpl_payment_otp_error';
            error.style.color = 'red';
            error.style.marginBottom = '0';
            error.textContent = message;

            document.getElementById('bnpl_payment_otp').parentElement.appendChild(error);
        }

        function removeError () {
            const errorElem = document.getElementById('bnpl_payment_otp_error');

            if (errorElem) {
                errorElem.remove();
            }
        }

        function getDeliveryDialogContent() {
            return `
            <table style="width:100%" class="adm-s-order-table-ddi-table">
                <thead style="text-align: left;">
                    <tr>
                        <td></td>
                        <td>Название</td>
                        <td id="table-head-amount">
                            Количество
                            <span data-hint="Количество товаров, которое доставляется"></span>
                        </td>
                        <td>Цена</td>
                    </tr>
                    <?php foreach ($__order->getBasket() as $item) {?>
                        <tr style="height:50px;">
                            <td>
                                <input type="checkbox" value="<?=$item->getId()?>" name="part_delivery_item">
                            </td>
                            <td style="min-width: 250px;">
                                <?=$item->getField('NAME')?>
                            </td>
                            <td>
                                <input style="min-width:50px;" id="part_delivery_item_qual_<?=$item->getId()?>"
                                       type="number"
                                       min="1"
                                       max="<?=(int) $item->getQuantity()?>"
                                       value="<?=(int) $item->getQuantity()?>"> шт
                            </td>
                            <td>
                                <span class="view_price">
                                    <div id="part_delivery_item_price_<?=$item->getId()?>" style="white-space: nowrap;">
                                        <?=CCurrencyLang::CurrencyFormat($item->getPrice(), $item->getField('CURRENCY'))?>
                                    </div>
                                </span>
                            </td>
                        </tr>
                    <?php } ?>
                </thead>
            </table>
        `;
        }

        function getDeliveryItems() {
            const items = document.querySelectorAll('input[type=checkbox][name=part_delivery_item]');
            const returnItems = {};

            for (const item of items) {
                if (!item.checked) continue;

                returnItems[item.value] = +document.getElementById('part_delivery_item_qual_' + item.value).value;
            }
            return returnItems;
        }

        function showDeliveryDialog(obj, field, status, params) {
            if (!deliveryPopup) {
                deliveryPopup = new BX.CDialog({
                    content: getDeliveryDialogContent(),
                    title: 'Выбор доставленных товаров',
                    resizable: false,
                    draggable: false,
                    buttons: [
                        {
                            title: 'Доставить',
                            className: 'adm-btn-save',
                            id: 'adm-btn-part-delivery',
                            action () {
                                removeError();

                                deliveryItems = getDeliveryItems();
                                if (!Object.keys(deliveryItems).length) {
                                    setTimeout(() => this.enable(), 100);
                                    deliveryPopup.ShowError('Не выбраны позиции для доставки!');
                                    return;
                                }

                                this.disable();
                                BX.showWait();

                                let deliveryData = {
                                    'order_id': orderId,
                                    'items' : deliveryItems
                                }

                                fetch('/bitrix/admin/bnplpad_delivery.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                    body: JSON.stringify(deliveryData),
                                })
                                    .then(res => {
                                        if (res.status === 200) return res.json();
                                        throw new Error('Status: ' + res.status);
                                    })
                                    .then(data => {
                                        this.enable();
                                        // hideLoader();
                                        BX.closeWait();
                                        if (!data.success) {
                                            BX.UI.Notification.Center.notify({
                                                content: 'An error occurred',
                                                position: 'top-right',
                                            });
                                            return;
                                        }

                                        if (!data.otp) {
                                            deliveryPopup.Close();
                                            updateShipmentStatus.call(obj, field, status, params);
                                            return;
                                        }

                                        const scrollY = document.documentElement.scrollTop;
                                        const otpPopup = new BX.CDialog({
                                            content: '<input name="otp" id="bnpl_payment_otp" type="text" maxlength="4" minlength="4" placeholder="Enter SMS code" style="margin: auto;display: block" oninput="document.getElementById(\'adm-btn-check-otp\').disabled = !(/^\\d{4}$/.test(this.value))">',
                                            title: 'Check OTP',
                                            resizable: false,
                                            draggable: false,
                                            height: 0,
                                            width: 0,
                                            buttons: [
                                                {
                                                    title: 'Check',
                                                    className: 'adm-btn-save',
                                                    id: 'adm-btn-check-otp',
                                                    action () {
                                                        this.disable();
                                                        removeError();

                                                        const otp = document.getElementById('bnpl_payment_otp').value;

                                                        otpData = {
                                                            'order_id': orderId,
                                                            'otp': otp,
                                                            'items': deliveryItems
                                                        }

                                                        fetch('/bitrix/admin/bnplpad_delivery_check_otp.php', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/x-www-form-urlencoded',
                                                                'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                                                                'X-Requested-With': 'XMLHttpRequest',
                                                            },
                                                            body: JSON.stringify(otpData),
                                                        })
                                                            .then(res => {
                                                                if (res.status === 200) return res.json();
                                                                throw new Error('Status: ' + res.status);
                                                            })
                                                            .then(() => {
                                                                this.enable();
                                                                otpPopup.Close();
                                                                deliveryPopup.Close();
                                                                updateShipmentStatus.call(obj, field, status, params);
                                                            })
                                                            .catch(err => {
                                                                console.log(err);
                                                                setTimeout(() => this.enable(), 100);
                                                                showError('An error occurred. Please try again.');
                                                            });
                                                    },
                                                },
                                                BX.CDialog.btnCancel,
                                            ],
                                        });

                                        BX.addCustomEvent(otpPopup, 'onWindowRegister', () => {
                                            const input = document.getElementById('bnpl_payment_otp');
                                            const content = input.closest('.bx-core-adm-dialog-content');

                                            input.focus();
                                            document.getElementById('adm-btn-check-otp').disabled = true;

                                            content.style.width = 'auto';
                                            content.style.height = 'auto';
                                        });

                                        BX.addCustomEvent(otpPopup, 'onWindowUnRegister', () => {
                                            document.getElementById('bnpl_payment_otp').remove();
                                            removeOtpError();
                                            document.documentElement.scrollTop = scrollY;
                                        });

                                        otpPopup.Show();
                                    })
                                    .catch(err => {
                                        console.error(err);
                                        BX.closeWait();
                                        BX.UI.Notification.Center.notify({
                                            content: 'An error occurred. Please try again.',
                                            position: 'top-right',
                                        });
                                    });
                            },
                        },
                        BX.CDialog.btnCancel,
                    ],
                });
                BX.addCustomEvent(deliveryPopup, 'onWindowRegister', () => {
                    deliveryPopup.GetContent().style.height = 'auto';
                });
            }
            deliveryPopup.Show();
        }

        BX.Sale.Admin.OrderShipment.prototype.updateShipmentStatus = function (field, status, params) {
            if (field !== 'STATUS_ID' || status !== 'DF') {
                updateShipmentStatus.call(this, field, status, params);
                return;
            }
            showDeliveryDialog(this, field, status, params);
        };
    });
</script>