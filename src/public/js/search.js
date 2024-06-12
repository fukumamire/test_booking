document.addEventListener('DOMContentLoaded', function () {
  const searchForm = document.getElementById('searchForm');
  searchForm.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      searchForm.submit();
    }
  });
});
