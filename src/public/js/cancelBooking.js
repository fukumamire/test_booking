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

// 予約変更　モーダルウィンドウ画面にて
let modalOpen = false; // モーダルウィンドウが開いているかどうかのフラグ

function openModal(id) {
  const modal = document.getElementById(`changeModal${id}`);
  modal.style.display = "block";

  modalOpen = true; // モーダルウィンドウを開いたときにフラグを true に設定

  const closeButton = document.querySelector(`.close-button`);
  closeButton.onclick = function () {
    modal.style.display = "none";
    modalOpen = false; // モーダルウィンドウを閉じたときにフラグを false に設定
  }

  // ユーザーがモーダルの外側をクリックしたときにモーダルを閉じる
  window.onclick = function (event) {
    if (modalOpen && event.target == modal) {
      modal.style.display = "none";
      modalOpen = false; // モーダルウィンドウを閉じたときにフラグを false に設定
    }
  }
}

// 予約変更ボタンにイベントリスナーを追加
document.querySelectorAll('.form__button--change').forEach(button => {
  button.addEventListener('click', function () {
    openModal(this.getAttribute('data-id'));
  });
});