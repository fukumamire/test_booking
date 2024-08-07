document.addEventListener('DOMContentLoaded', function () {
  const bookingStatusTab = document.getElementById('booking-status');
  const bookingHistoryTab = document.getElementById('booking-history');
  const bookingContentWrap = document.querySelector('.booking__content-wrap');
  const bookingHistoryWrap = document.querySelector('.booking__history-wrap');

  function showTabContent() {
    if (bookingStatusTab.checked) {
      bookingContentWrap.style.display = 'block';
      bookingHistoryWrap.style.display = 'none';
    } else if (bookingHistoryTab.checked) {
      bookingContentWrap.style.display = 'none';
      bookingHistoryWrap.style.display = 'block';
    }
  }

  bookingStatusTab.addEventListener('change', showTabContent);
  bookingHistoryTab.addEventListener('change', showTabContent);

  // 初期表示を設定
  showTabContent();
});


// document.addEventListener('DOMContentLoaded', function () {
//   const bookingStatusTab = document.getElementById('booking-status');
//   const bookingHistoryTab = document.getElementById('booking-history');
//   const bookingContentWrap = document.querySelector('.booking__content-wrap');
//   const bookingHistoryWrap = document.querySelector('.booking__history-wrap');

//   bookingStatusTab.addEventListener('change', function () {
//     bookingContentWrap.style.display = 'block';
//     bookingHistoryWrap.style.display = 'none';
//   });

//   bookingHistoryTab.addEventListener('change', function () {
//     bookingContentWrap.style.display = 'none';
//     bookingHistoryWrap.style.display = 'block';
//   });

// });




// document.addEventListener('DOMContentLoaded', function() {
//   const bookingStatusTab = document.getElementById('booking-status');
//   const bookingHistoryTab = document.getElementById('booking-history');
//   const bookingStatusContent = document.querySelector('.booking__content-wrap');
//   const bookingHistoryContent = document.querySelector('.booking__history-wrap');

//   bookingStatusTab.addEventListener('click', function() {
//     bookingStatusContent.style.display = 'block'; // 予約状況を表示
//     bookingHistoryContent.style.display = 'none'; // 予約履歴を非表示
//   });

//   bookingHistoryTab.addEventListener('click', function() {
//     bookingStatusContent.style.display = 'none'; // 予約状況を非表示
//     bookingHistoryContent.style.display = 'block'; // 予約履歴を表示
//   });
// });