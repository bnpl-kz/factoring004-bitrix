<script>
  document.addEventListener('DOMContentLoaded', () => {
    const updateShipmentStatus = BX.Sale.Admin.OrderShipment.prototype.updateShipmentStatus;
    const orderId = '<?=$__order->getId()?>';

    function showLoader () {
      const elem = document.createElement('div');
      elem.setAttribute('style', 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: #000; z-index: 9999; opacity: 0.5;');
      elem.id = 'bnpl_payment_delivery_send_overlay';

      document.body.appendChild(elem);
      BX.showWait();
    }

    function hideLoader () {
      document.getElementById('bnpl_payment_delivery_send_overlay').remove();
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

    BX.Sale.Admin.OrderShipment.prototype.updateShipmentStatus = function (field, status, params) {
      if (field !== 'STATUS_ID' || status !== 'DF') {
        updateShipmentStatus.call(this, field, status, params);
        return;
      }

      showLoader();
      const self = this;

      fetch('/bitrix/admin/bnplpayment_delivery.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: 'order_id=' + encodeURIComponent(orderId),
      })
        .then(res => {
          if (res.status === 200) return res.json();
          throw new Error('Status: ' + res.status);
        })
        .then(data => {
          hideLoader();

          if (!data.success) {
            BX.UI.Notification.Center.notify({
              content: 'An error occurred',
              position: 'top-right',
            });
            return;
          }

          if (!data.otp) {
            updateShipmentStatus.call(self, field, status, params);
            return;
          }

          const scrollY = document.documentElement.scrollTop;
          const popup = new BX.CDialog({
            content: '<input name="otp" id="bnpl_payment_otp" type="text" maxlength="4" minlength="4" placeholder="Enter SMS code" style="margin: auto;display: block" oninput="document.getElementById(\'adm-btn-check-otp\').disabled = !(/^\\d{4}$/.test(this.value))">',
            title: 'Check OTP',
            resizable: false,
            draggable: false,
            buttons: [
              {
                title: 'Check',
                className: 'adm-btn-save',
                id: 'adm-btn-check-otp',
                action () {
                  this.disable();
                  removeError();

                  const otp = document.getElementById('bnpl_payment_otp').value;

                  fetch('/bitrix/admin/bnplpayment_delivery_check_otp.php', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                      'X-Bitrix-Csrf-Token': '<?=bitrix_sessid()?>',
                      'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: 'order_id=' + encodeURIComponent(orderId) + '&otp=' + encodeURIComponent(otp),
                  })
                    .then(res => {
                      if (res.status === 200) return res.json();
                      throw new Error('Status: ' + res.status);
                    })
                    .then(() => {
                      this.enable();
                      popup.Close();
                      updateShipmentStatus.call(self, field, status, params);
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
          });

          popup.Show();
        })
        .catch(err => {
          console.error(err);

          hideLoader();
          BX.UI.Notification.Center.notify({
            content: 'An error occurred. Please try again.',
            position: 'top-right',
          });
        });
    };
  });
</script>