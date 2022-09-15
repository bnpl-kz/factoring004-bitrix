<script>
  const showReturnWindow = BX.Sale.Admin.OrderPayment.prototype.showReturnWindow;

  BX.Sale.Admin.OrderPayment.prototype.showReturnWindow = function(action) {
    showReturnWindow.call(this, action);

    const modal = this.rtWindow;
    const orderId = '<?=$__order->getId()?>';

    const buttons = this.rtWindow.PARAMS.buttons;
    const originalAction = buttons[0].action;

    buttons[0].action = function () {
      this.disable();
      modal.hideNotify();

      fetch('/bitrix/admin/bnplpayment_return.php', {
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
          if (!data.success) {
            setTimeout(() => this.enable(), 100);
            modal.ShowError(data.response.message)
            return;
          }

          if (!data.otp || data.cancel) {
            originalAction.call(this);
            return;
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

          const self = this;
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

                  fetch('/bitrix/admin/bnplpayment_return_check_otp.php', {
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
                      setTimeout(() => this.enable(), 100);
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
          setTimeout(() => this.enable(), 100);
          modal.ShowError('An error occurred. Please try again!');
          console.error(err);
        });
    };

    this.rtWindow.ClearButtons();
    this.rtWindow.SetButtons(buttons);
  };
</script>