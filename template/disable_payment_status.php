<script>
    BX.Sale.Admin.OrderPayment.prototype.initPaidPopup = function () {
        var generalStatusFields = BX.findChildrenByClassName(BX('PAYMENT_BLOCK_STATUS_INFO_' + this.index), 'not_paid', true);
        var returnStatusFields = BX.findChildrenByClassName(BX('PAYMENT_BLOCK_STATUS_INFO_' + this.index), 'return', true);

        var indexes = [this.index];
        if (this.viewForm) indexes.push(this.index + '_SHORT');
        var orderId = <?=$_GET['ID']?>;
        var partReturnTableContent = '<table style="width:100%" class="adm-s-order-table-ddi-table">';
        partReturnTableContent += '<thead style="text-align: left;"><tr><td></td><td>Название</td><td>Количество</td><td>Цена</td></tr></thead>';

        <?php foreach ($__order->getBasket() as $item) {?>
        partReturnTableContent += '<tr style="height:50px;"><td><input type="checkbox" value="<?=$item->getId()?>" name="part_return_item" ></td><td style="min-width: 250px;"><?=$item->getField('NAME')?></td><td><input name="part_return_item_qual_<?=$item->getId()?>" type="number" min="1" max="<?=(int) $item->getQuantity()?>" value="1" /> шт </td><td><span class="view_price"><div id="part_return_item_price_<?=$item->getId()?>" style="white-space: nowrap;"><?=$item->getField('PRICE')?> тенге</div></span></td></tr>'
        <?php } ?>
        partReturnTableContent += '</table>';
        partReturnTableContent += '<p color="red" id="bnpl_payment_part_return_error"></p>';

        var menu = [];

        if (Object.keys(this.psToReturn).length > 0) {
            menu.push(
                {
                    'ID': 'RETURN',
                    'TEXT': BX.message('PAYMENT_PAID_RETURN'),
                    'ONCLICK': BX.proxy(function () {
                        if (this.viewForm) {
                            this.showReturnWindow('return');
                        } else {
                            if (BX('PAYMENT_PAID_' + indexes[k]))
                                BX('PAYMENT_PAID_' + indexes[k]).value = 'N';

                            var isReturnChanged = BX('PAYMENT_IS_RETURN_CHANGED_' + indexes[k]);
                            if (isReturnChanged) {
                                isReturnChanged.value = 'Y';
                            }

                            var obOperation = BX('OPERATION_ID_' + this.index);
                            if (obOperation)
                                obOperation.disabled = false;

                            var isReturn = BX('PAYMENT_IS_RETURN_' + indexes[k]);
                            if (isReturn)
                                isReturn.value = 'Y';

                            this.changeNotPaidStatus('NO');

                            for (var i in generalStatusFields) {
                                if (!generalStatusFields.hasOwnProperty(i))
                                    continue;
                                BX.style(generalStatusFields[i], 'display', 'table-row');
                            }
                            for (var i in returnStatusFields) {
                                if (!returnStatusFields.hasOwnProperty(i))
                                    continue;
                                BX.style(returnStatusFields[i], 'display', 'table-row');
                            }

                            BX.bind(BX('OPERATION_ID_' + this.index), 'change', function () {
                                var tr = BX.findParent(this, { tag: 'tr' });
                                if (tr) {
                                    var style = (this.value != 'Y') ? 'none' : 'table-row';
                                    BX.style(tr.nextElementSibling, 'display', style);
                                }
                            });
                        }
                    }, this),
                },
            );
            menu.push(
                {
                    'ID': 'PART_RETURN',
                    'TEXT': 'Частичный возврат',
                    'ONCLICK': BX.proxy(function () {
                        dialogPartReturn = new BX.CDialog({
                            content: partReturnTableContent,
                            title: 'Выберите товар для возврата и укажите количество',
                            resizable: false,
                            draggable: false,
                            buttons: [{
                                title: 'Отправить',
                                className: 'adm-btn-save',
                                id: 'adm-btn-send-partreturn',
                                action() {
                                    this.disable();

                                    function getPartReturnAmount(items) {
                                        total = 0;
                                        for (i in items) {
                                            item = items[i];
                                            total += item.quant * item.price;
                                        }
                                        return total;
                                    }

                                    function getReturnItems() {
                                        items = document.querySelectorAll('input[type=checkbox][name=part_return_item]');
                                        returnItems = [];
                                        items.forEach(item => {
                                            if (item.checked) {
                                                basketID = item.value;
                                                quant = document.querySelector('input[name=part_return_item_qual_' + basketID + ']').value;
                                                price = parseInt(document.querySelector('#part_return_item_price_' + basketID).innerText);
                                                returnItems.push({'ID': basketID, 'quant': quant, 'price': price});
                                            }
                                        });
                                        return returnItems;
                                    }

                                    function showPartReturnError(msg) {
                                        errorField = document.getElementById('bnpl_payment_part_return_error');
                                        errorField.textContent = msg;
                                    }


                                    var returnItems = getReturnItems();
                                    if (returnItems.length == 0) {
                                        showPartReturnError('Не выбраны позиции для возврата!');
                                        return;
                                    }
                                    var amount = getPartReturnAmount(returnItems);
                                    var fetchBody = 'order_id=' + encodeURIComponent(orderId) + '&amount=' + encodeURIComponent(amount);
                                    fetchBody += '&returnItems=' + JSON.stringify(returnItems);
                                    fetch('/bitrix/admin/bnplpayment_return.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                            'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                                            'X-Requested-With': 'XMLHttpRequest',
                                        },
                                        body: fetchBody,
                                    })
                                        .then(res => {
                                            if (res.status === 200) return res.json();
                                            throw new Error('Status: ' + res.status);
                                        })
                                        .then(data => {
                                            if (!data.success) {
                                                this.enable();
                                                showPartReturnError(data.response.message)
                                                return;
                                            }

                                            if (!data.otp || data.cancel) {
                                                originalAction.call(this);
                                                return;
                                            }

                                            function showError(message) {
                                                const error = document.createElement('p');
                                                error.id = 'bnpl_payment_otp_error';
                                                error.style.color = 'red';
                                                error.style.marginBottom = '0';
                                                error.textContent = message;

                                                document.getElementById('bnpl_payment_otp').parentElement.appendChild(error);
                                            }

                                            function removeError() {
                                                const errorElem = document.getElementById('bnpl_payment_otp_error');

                                                if (errorElem) {
                                                    errorElem.remove();
                                                }
                                            }

                                            dialogPartReturn.Close();
                                            const self = this;
                                            const scrollY = document.documentElement.scrollTop;
                                            const popup = new BX.CDialog({
                                                content: '<input name="otp" id="bnpl_payment_otp" type="text" maxlength="4" minlength="4" placeholder="Enter SMS code" style="margin: auto;display: block" oninput="document.getElementById(\'adm-btn-check-otp\').disabled = !(/^\\d{4}$/.test(this.value))">',
                                                title: 'Check OTP',
                                                resizable: false,
                                                draggable: false,
                                                buttons: [{
                                                    title: 'Check',
                                                    className: 'adm-btn-save',
                                                    id: 'adm-btn-check-otp',
                                                    action() {
                                                        this.disable();
                                                        removeError();

                                                        const otp = document.getElementById('bnpl_payment_otp').value;

                                                        fetch('/bitrix/admin/bnplpayment_return_check_otp.php', {
                                                            method: 'POST',
                                                            headers: {
                                                                'Content-Type': 'application/x-www-form-urlencoded',
                                                                'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                                                                'X-Requested-With': 'XMLHttpRequest',
                                                            },
                                                            body: 'order_id=' + encodeURIComponent(orderId) + '&otp=' + encodeURIComponent(otp) + '&amount=' + encodeURIComponent(amount) + '&returnItems=' + JSON.stringify(returnItems),
                                                        })
                                                            .then(res => {
                                                                if (res.status === 200) return res.json();
                                                                console.log(res.text());
                                                                throw new Error('Status: ' + res.status);
                                                            })
                                                            .then(() => {
                                                                this.enable();
                                                                popup.Close();
                                                                originalAction.call(self);
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

                                            BX.addCustomEvent(popup, 'onWindowRegister', () => {
                                                const input = document.getElementById('bnpl_payment_otp');
                                                const content = input.closest('.bx-core-adm-dialog-content');

                                                input.focus();
                                                document.getElementById('adm-btn-check-otp').disabled = true;

                                                content.style.width = 'auto';
                                                content.style.height = 'auto';
                                            });

                                            BX.addCustomEvent(popup, 'onWindowUnRegister', () => {
                                                document.getElementById('bnpl_payment_otp').remove();
                                                removeError();
                                                document.documentElement.scrollTop = scrollY;
                                                setTimeout(() => self.enable(), 100);
                                            });

                                            popup.Show();
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
                        dialogPartReturn.Show()
                    }, this),
                });
        }

        if (!this.viewForm) {
            menu.unshift(
                {
                    'ID': 'PAID',
                    'TEXT': BX.message('PAYMENT_PAID_YES'),
                    'ONCLICK': BX.proxy(function () {
                        if (this.viewForm) {
                            this.showWindowPaidPayment();
                        } else {
                            var paymentPaid = BX('PAYMENT_PAID_' + indexes[k]);
                            if (paymentPaid)
                                paymentPaid.value = 'Y';

                            var isReturnChanged = BX('PAYMENT_IS_RETURN_CHANGED_' + indexes[k]);
                            if (isReturnChanged) {
                                isReturnChanged.value = 'N';
                            }

                            this.changePaidStatus('YES');

                            var obOperation = BX('OPERATION_ID_' + this.index);
                            if (obOperation)
                                obOperation.options[obOperation.selectedIndex].value = '';

                            for (var i in generalStatusFields) {
                                if (!generalStatusFields.hasOwnProperty(i))
                                    continue;
                                BX.style(generalStatusFields[i], 'display', 'none');
                            }
                            for (var i in returnStatusFields) {
                                if (!returnStatusFields.hasOwnProperty(i))
                                    continue;
                                BX.style(returnStatusFields[i], 'display', 'none');
                            }
                        }
                    }, this),
                },
            );
        }

        for (var k in indexes) {
            if (!indexes.hasOwnProperty(k))
                continue;
            var act = new BX.COpener({
                DIV: BX('BUTTON_PAID_' + indexes[k]).parentNode,
                MENU: menu,
            });
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const paymentSelect = document.querySelector('[name="PAYMENT[1][PAY_SYSTEM_ID]"]');

        if (paymentSelect) {
            const input = document.createElement('input');

            input.name = paymentSelect.name;
            input.value = paymentSelect.value;
            input.type = 'hidden';
            input.id = paymentSelect.id;

            paymentSelect.disabled = true;
            paymentSelect.parentElement.appendChild(input);
        }
    });
</script>