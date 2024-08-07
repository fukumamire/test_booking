document.addEventListener('DOMContentLoaded', function () {
  const bookingStatusTab = document.getElementById('booking-status');
  const bookingHistoryTab = document.getElementById('booking-history');
  const bookingContentWrap = document.querySelector('.booking__content-wrap');
  const bookingHistoryWrap = document.querySelector('.booking__history-wrap');

  function showTabContent() {
    if (bookingStatusTab.checked) {
      bookingContentWrap.style.display = 'block'; // 予約状況タブが選択されたとき
      bookingHistoryWrap.style.display = 'none';
    } else if (bookingHistoryTab.checked) {
      bookingContentWrap.style.display = 'none'; // 予約履歴タブが選択されたとき
      bookingHistoryWrap.style.display = 'block';
    }
  }

  bookingStatusTab.addEventListener('change', showTabContent);
  bookingHistoryTab.addEventListener('change', showTabContent);

  // 初期表示を設定
  showTabContent(); // ページ読み込み時に初期表示を設定
});