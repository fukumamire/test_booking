// toggleFavorite.js

async function toggleFavorite(button, shopId) {
  const isLoggedIn = await checkLoginStatus(); // ユーザーがログインしているかどうかを判定

  if (!isLoggedIn) {
    window.location.href = '/request_login'; // ログインしていない場合はログインページへリダイレクト
    return;
  }

  const isFavorite = await checkIsFavorite(shopId); // お気に入り状態を判定
  const url = isFavorite ? `/shops/${shopId}/unfavorite` : `/shops/${shopId}/favorite`;
  const method = isFavorite ? 'DELETE' : 'POST';

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
      button.classList.toggle('heart-active'); // ここではクラス名を適宜変更してください
    } else {
      console.error('Error toggling favorite:', data.error);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

async function checkIsFavorite(shopId) {
  const response = await fetch(`/api/shops/${shopId}/is-favorite`);
  const data = await response.json();
  return data.is_favorite;
}

async function checkLoginStatus() {
  const response = await fetch('/api/check-login-status');
  const data = await response.json();
  return data.isLoggedIn;
}