document.querySelectorAll('.form__button--cancel').forEach(function (button) {
  button.addEventListener('click', function (event) {
    event.preventDefault(); // ボタンのデフォルト動作を防止

    // 確認ダイアログを表示
    const confirmation = confirm("予約をキャンセルしますか？");

    if (confirmation) {
      // ユーザーが「OK」をクリックした場合、対応するフォームを送信
      const formId = this.getAttribute('data-id'); // 予約IDを取得
      const form = document.getElementById(`cancelForm${formId}`);
      form.submit();
    } else {
      // 「キャンセル」をクリックした場合、何もしない
      console.log("キャンセルを取り消しました。");
    }
  });
});