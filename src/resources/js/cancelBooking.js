document.addEventListener('DOMContentLoaded', function () {
  const cancelForms = document.querySelectorAll('.header__form');
  cancelForms.forEach(form => {
    form.addEventListener('submit', function (event) {
      event.preventDefault(); // デフォルトのフォーム送信を防止
      fetch(form.action, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, // CSRFトークンを設定
        }
      }).then(response => {
        if (response.ok) {
          window.location.href = '/mypage'; // 成功したら /mypage にリダイレクト
        } else {
          console.log('予約のキャンセルに失敗しました');
        }
      }).catch(error => {
        console.error('ネットワークエラー:', error);
      });
    });
  });
});