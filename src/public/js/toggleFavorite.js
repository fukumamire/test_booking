async function toggleFavorite(button, shopId) {
  // ログイン状況をチェック
  const isLoggedIn = document.querySelector('meta[name="login-status"]').getAttribute('content') === 'true';
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); // CSRFトークンを取得

  if (!isLoggedIn) {
    window.location.href = '/request_login'; // ログインページへのパスに変更
    return;
  }

  // 現在のページがマイページのお気に入り一覧ページかどうかを判別するため具体的な条件の設定
  const isMyFavoritePage = window.location.pathname === '/mypage'; // マイページのお気に入り一覧のURL

  // マイページのお気に入り一覧ページで、お気に入りに登録されている場合は何もしない
  if (isMyFavoritePage && button.classList.contains('heart-active')) {
    console.log('Already favorited, no action taken.');
    return;
  }

  const url = `/api/shops/${shopId}/toggle-favorite`;
  const method = 'POST';

  try {
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`, // ここでtokenを使用
        'X-CSRF-TOKEN': token, // CSRFトークンを設定
      },
    });

    if (!response.ok) {
      if (response.status === 401) {
        window.location.href = '/request_login';
        return;
      }
      throw new Error('Network response was not ok');
    }

    const data = await response.json();

    if (data.success) {
      button.classList.toggle('heart-active');
    } else {
      console.error('Error toggling favorite:', data.error);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}