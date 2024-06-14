document.addEventListener('DOMContentLoaded', function () {
  const searchForm = document.getElementById('search__form');
  searchForm.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      event.preventDefault();
      searchForm.submit();
    }
  });
});
