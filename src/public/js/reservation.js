document.addEventListener('DOMContentLoaded', function () {
  var dateInput = document.getElementById('datePicker');
  var today = new Date().toISOString().split('T')[0];
  dateInput.setAttribute('min', today);

  dateInput.addEventListener('change', function () {
    document.getElementById('dateSummary').textContent = this.value;
  });

  document.querySelector('select[name="time"]').addEventListener('change', function () {
    document.getElementById('timeSummary').textContent = this.value;
  });

  document.querySelector('select[name="number"]').addEventListener('change', function () {
    document.getElementById('numberSummary').textContent = this.value;
  });
});