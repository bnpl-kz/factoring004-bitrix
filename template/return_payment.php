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
          this.enable();

          console.log(data);

          if (!data.success) {
            modal.ShowError(data.response.message)
            return;
          }

          originalAction.call(this);
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