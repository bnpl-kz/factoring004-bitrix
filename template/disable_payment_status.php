<?php
    require_once __DIR__ . '/partial_return.php';
?>
<script>
  BX.Sale.Admin.OrderPayment.prototype.initPaidPopup = function () {
    var generalStatusFields = BX.findChildrenByClassName(BX('PAYMENT_BLOCK_STATUS_INFO_' + this.index), 'not_paid', true);
    var returnStatusFields = BX.findChildrenByClassName(BX('PAYMENT_BLOCK_STATUS_INFO_' + this.index), 'return', true);

    var indexes = [this.index];
    if (this.viewForm) indexes.push(this.index + '_SHORT');

    var menu = [];

    this.initFactoring004PartialRefund();

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
        this.getFactoring004PartialRefundMenuItem(),
      );
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