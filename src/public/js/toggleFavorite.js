async function toggleFavorite(button, shopId) {
  const isLoggedIn = await checkLoginStatus(); // ユーザーがログインしているかどうかを判定

  if (!isLoggedIn) {
    window.location.href = '/request_login'; // ログインしていない場合は会員登録　ログインへリダイレクト
    return;
  }

  const url = `/shops/${shopId}/toggle-favorite`; // 常にこのURLを使用
  const method = 'POST'; // メソッドはPOST固定

  try {
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    });

    const data = await response.json();

    if (data.success) {
      // UIの更新処理
      // 例: ボタンのクラスを切り替えるなど
      button.classList.toggle('heart-active'); // ここではクラス名を適宜変更
    } else {
      console.error('Error toggling favorite:', data.error);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

async function checkLoginStatus() {
  const response = await fetch('/api/check-login-status');
  const data = await response.json();
  return data.isLoggedIn;
}