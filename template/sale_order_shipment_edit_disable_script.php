<script>
  document.addEventListener('DOMContentLoaded', () => {
    const select = document.getElementById('SHIPMENT_STATUS_ID');
    const delivery = document.getElementById('DELIVERY_1');

    select.disabled = true;
    delivery.disabled = true;

    console.log('Disabled by plugin');
  });
</script>