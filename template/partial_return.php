<?php
    \Bitrix\Main\UI\Extension::load('ui.hint');

    /** @var \Bitrix\Sale\Order $__order */
    $notShown = ($__order->getBasket()->count() === 1
        && $__order->getBasket()->getItemByIndex(0)->getField('QUANTITY') <= 1)
        || ($__order->getShipmentCollection()[0]->getField('STATUS_ID') !== 'DF')
?>
<script>
  BX.Sale.Admin.OrderPayment.prototype.initFactoring004PartialRefund = function () {
    const notShown = <?=$notShown ? 'true' : 'false'?>;

    this._factoring004PartialRefundTableView = notShown ? null : this._getFactoring004PartialRefundTableView();
  };

  BX.Sale.Admin.OrderPayment.prototype.getFactoring004PartialRefundMenuItem = function () {
    if (!this._factoring004PartialRefundTableView) return null;
    
    const self = this;

    return {
      'ID': 'PART_RETURN',
      'TEXT': 'Частичный возврат',
      'ONCLICK': BX.proxy(function () {
        const dialog = new BX.CDialog({
          content: this._factoring004PartialRefundTableView,
          title: 'Выберите товар для возврата и укажите количество',
          resizable: false,
          draggable: false,
          buttons: [{
            title: 'Отправить',
            className: 'adm-btn-save',
            id: 'adm-btn-send-partreturn',
            action () {
              self._factoring004PartialRefund(dialog, this);
            },
          },
            BX.CDialog.btnCancel,
          ],
        });
        
        dialog.Show();
        BX.UI.Hint.init(BX('table-head-amount'));
      }, this),
    }
  };

    /**
     * @param {BX.CDialog} dialog
     * @param {BX.CWindowButton} button
     * @returns {void}
     * @private
     */
    BX.Sale.Admin.OrderPayment.prototype._factoring004PartialRefund = function (dialog, button) {
      button.disable();

      const orderId = '<?=$__order->getId()?>';

      function getReturnItems () {
        const items = document.querySelectorAll('input[type=checkbox][name=part_return_item]');
        const returnItems = {};

        for (const item of items) {
          if (!item.checked) continue;

          returnItems[item.value] = +document.getElementById('part_return_item_qual_' + item.value).value;
        }

        return returnItems;
      }

      function request (url, data) {
        return fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
            'X-Requested-With': 'XMLHttpRequest',
          },
          body: JSON.stringify(data),
        });
      }

      function showOtpError (message) {
        const error = document.createElement('p');
        error.id = 'bnpl_payment_otp_error';
        error.style.color = 'red';
        error.style.marginBottom = '0';
        error.textContent = message;

        document.getElementById('bnpl_payment_otp').parentElement.appendChild(error);
      }

      function removeOtpError () {
        const errorElem = document.getElementById('bnpl_payment_otp_error');

        if (errorElem) {
          errorElem.remove();
        }
      }

      function handleSuccess () {
        window.location.reload();
      }

      function showOtpModal (returnItems) {
        const scrollY = document.documentElement.scrollTop;
        const popup = new BX.CDialog({
          content: `<input name="otp"
                                     id="bnpl_payment_otp"
                                     type="text"
                                     maxlength="4"
                                     minlength="4"
                                     placeholder="Enter SMS code"
                                     style="margin: auto; display: block"
                                     oninput="document.getElementById('adm-btn-check-otp').disabled = !(/^\\d{4}$/.test(this.value))">`,
          title: 'Check OTP',
          resizable: false,
          draggable: false,
          buttons: [{
            title: 'Check',
            className: 'adm-btn-save',
            id: 'adm-btn-check-otp',
            async action () {
              this.disable();
              removeOtpError();

              const otp = document.getElementById('bnpl_payment_otp').value;
              let data = {};

              try {
                const response = await request('/bitrix/admin/bnplpayment_return_check_otp.php', {
                  order_id: orderId,
                  otp,
                  returnItems,
                });

                data = await response.json();

                if (!response.ok) {
                  throw new Error();
                }
              } catch (e) {
                setTimeout(() => this.enable(), 100);
                showOtpError(data.error || 'An error occurred. Please try again.');
                return;
              }

              popup.Close();
              popup.SetContent('');
              handleSuccess();
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
          removeOtpError();
          document.documentElement.scrollTop = scrollY;
        });

        popup.Show();
      }

      async function handle () {
        const returnItems = getReturnItems();

        if (!Object.keys(returnItems).length) {
          setTimeout(() => button.enable(), 100);
          dialog.ShowError('Не выбраны позиции для возврата!');
          return;
        }

        let data = {};

        try {
          const response = await request('/bitrix/admin/bnplpayment_return.php', {
            order_id: orderId,
            returnItems,
          });

          data = await response.json();

          if (!response.ok || !data.success) {
            throw new Error();
          }
        } catch (e) {
          setTimeout(() => button.enable(), 100);
          dialog.ShowError(data.error || 'An error occurred. Please try again.');
          return;
        }

        if (!data.otp || data.cancel) {
          handleSuccess();
          return;
        }

        dialog.Close();
        dialog.SetContent('');
        showOtpModal(returnItems);
      }

      handle().catch(e => console.log(e));
    };

    /**
     * @returns {string}
     * @private
     */
    BX.Sale.Admin.OrderPayment.prototype._getFactoring004PartialRefundTableView = function () {
        return `
            <table style="width:100%" class="adm-s-order-table-ddi-table">
                <thead style="text-align: left;">
                    <tr>
                        <td></td>
                        <td>Название</td>
                        <td id="table-head-amount">
                            Количество
                            <span data-hint="Количество товаров, которое необходимо оставить"></span>
                        </td>
                        <td>Цена</td>
                    </tr>
                    <?php foreach ($__order->getBasket() as $item) {?>
                        <tr style="height:50px;">
                            <td>
                                <input type="checkbox" value="<?=$item->getId()?>" name="part_return_item">
                            </td>
                            <td style="min-width: 250px;">
                                <?=$item->getField('NAME')?>
                            </td>
                            <td>
                                <input id="part_return_item_qual_<?=$item->getId()?>"
                                       type="number"
                                       min="1"
                                       max="<?=(int) $item->getQuantity()?>"
                                       value="<?=(int) $item->getQuantity()?>"> шт
                            </td>
                            <td>
                                <span class="view_price">
                                    <div id="part_return_item_price_<?=$item->getId()?>" style="white-space: nowrap;">
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
</script>