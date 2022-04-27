<script>
    document.addEventListener('DOMContentLoaded', () => {
      const selectAction = document.getElementById('ACTION_FILE');

      /**
       * @param {string} name
       * @param {string} value
       * @param {boolean} disabled
       * @returns {HTMLTextAreaElement}
       */
      function createTextarea(name, value, disabled) {
        const textarea = document.createElement('textarea');

        textarea.name = name;
        textarea.value = value;
        textarea.disabled = disabled;
        textarea.rows = 5;
        textarea.cols = 30;
        textarea.onchange = e => bizvalChangeValue(e.target);
        textarea.style.verticalAlign = 'middle';

        return textarea;
      }

      function convertTokenInputs() {
        const elementNames = ['BNPL_PAYMENT_API_OAUTH_PREAPP_TOKEN', 'BNPL_PAYMENT_API_OAUTH_ACCOUNTING_SERVICE_TOKEN'];

        for (const elemId of elementNames) {
          const input = document.querySelector(`input[type="text"][name*="${elemId}"]`);
          const checkbox = document.querySelector(`input[type="checkbox"][name*="${elemId}"]`);
          const select = document.querySelector(`select[name*="${elemId}"]`);
          const selectClickHandler = select.onchange;

          if (!input) continue;

          let textarea = createTextarea(input.name, input.value, checkbox.checked);

          input.parentElement.insertBefore(textarea, input);
          input.remove();

          checkbox.onclick = () => {
            if (checkbox.checked) {
              textarea.value = checkbox.dataset.defaultValue || '';
              textarea.disabled = true;
              select.disabled = true;
            } else {
              textarea.value = checkbox.dataset.initialValue || '';
              textarea.disabled = false
              select.disabled = false
            }
          };

          select.onchange = e => {
            if (e.target.value !== 'VALUE') {
              selectClickHandler.call(e.target, e);
              return;
            }

            const elements = e.target.closest('td').querySelectorAll(`select[name*="${elemId}"]`);

            for (const elem of elements) {
              if (elem !== e.target) {
                textarea = createTextarea(input.name, input.value);

                elem.parentElement.insertBefore(textarea, elem);
                elem.remove();
                break;
              }
            }
          };
        }
      }

      selectAction.addEventListener('change', e => {
        if (e.target.value === 'bnplpayment') {
          setTimeout(convertTokenInputs, 100);
        }
      });

      selectAction.dispatchEvent(new Event('change'));
    });
</script>