document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("reservationForm");
  if (form.classList.contains("not-authenticated")) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      window.location.href = form.dataset.loginUrl;
    });
  }

  var dateInput = document.getElementById("datePicker");
  var today = new Date().toISOString().split("T")[0];
  dateInput.setAttribute("min", today);

  dateInput.addEventListener("change", function () {
    document.getElementById("dateSummary").textContent = this.value
  });

  document
    .querySelector('select[name="time"]')
    .addEventListener("change", function () {
      document.getElementById("timeSummary").textContent = this.value;
    });

  document
    .querySelector('select[name="number"]')
    .addEventListener("change", function () {
      document.getElementById("numberSummary").textContent = this.value;
    });
});
