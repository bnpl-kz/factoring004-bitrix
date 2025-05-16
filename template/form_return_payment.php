<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('order_payment_edit_info_form');
    const orderId = '<?=$__order->getId()?>';

    function submitForm() {
      form.removeEventListener('submit', handleForm);
      form.submit();
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

    function handleForm (e) {
      e.preventDefault();

      const input = this.elements['PAYMENT[1][IS_RETURN]'];
      const enableButton = () => {
        const button = form.querySelector('[type="submit"].adm-btn-load');

        button.disabled = false;
        button.classList.remove('adm-btn-load');

        for (const className of ['.adm-btn-load-img-green', '.adm-btn-load-img']) {
          const elem = button.parentElement.querySelector(className);

          if (elem) elem.remove();
        }
      }

      if (input.value !== 'Y') {
        submitForm();
        return;
      }

      fetch('/bitrix/admin/bnplpad_return.php', {
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
            enableButton();
            BX.UI.Notification.Center.notify({
              content: data.response.message,
              position: 'top-right',
            });
            return;
          }

          if (!data.otp || data.cancel) {
            submitForm();
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

                  fetch('/bitrix/admin/bnplpad_return_check_otp.php', {
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
                      submitForm();
                    })
                    .catch(err => {
                      console.error(err);
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
            enableButton();
            document.documentElement.scrollTop = scrollY;
          });

          popup.Show();
        })
        .catch(err => {
          setTimeout(() => enableButton(), 100);
          BX.UI.Notification.Center.notify({
            content: 'An error occurred',
            position: 'top-right',
          });
          console.error(err);
        });
    }

    form.addEventListener('submit', handleForm);
  });
</script>