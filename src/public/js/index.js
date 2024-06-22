$(document).ready(function () {
  $('.heart').on('click', function () {
    $(this).toggleClass('heart-active'); // クラスを切り替える

    // ここでサーバーへのAjaxリクエストを追加して、DBの状態を同期的に更新することも可能です。
    // 例: $.ajax({url: '/update_favorite', method: 'POST', data: {status: $(this).hasClass('heart-active'),...});
  });
});