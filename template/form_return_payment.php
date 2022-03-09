<script>
  document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('order_payment_edit_info_form');
    const orderId = '<?=$__order->getId()?>';

    function submitForm() {
      form.removeEventListener('submit', handleForm);
      form.submit();
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
            enableButton();
            BX.UI.Notification.Center.notify({
              content: data.response.message,
              position: 'top-right',
            });
            return;
          }

          submitForm();
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