let token = localStorage.getItem('authToken'); // ローカルストレージからトークンを取得

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.heart,.heart-active').forEach(function (element) {
    element.addEventListener('click', function () {
      toggleFavorite(this, element.dataset.shopId); // data-shop-id属性を使用してshopIdを取得
    });
  });

  async function toggleFavorite(button, shopId) {
    let isLoggedIn = await checkLoginStatus(); // ユーザーがログインしているかどうかをチェック

    if (!isLoggedIn) {
      window.location.href = '/request_login'; // ログインページへのパスに変更
      return;
    }

    const url = `/shops/${shopId}/toggle-favorite`; // 常にこのURLを使用
    const method = 'POST'; // メソッドはPOST固定

    try {
      const response = await fetch(url, {
        method,
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`, // tokenはサーバーから取得した認証トークン
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
      });

      if (!response.ok) {
        if (response.status === 401) {
          window.location.href = '/request_login'; // ログインページにリダイレクト
          return;
        }
        throw new Error('Network response was not ok');
      }

      const data = await response.json();

      if (data.success) {
        button.classList.toggle('heart-active'); // クラスを切り替える
      } else {
        console.error('Error toggling favorite:', data.error);
      }
    } catch (error) {
      console.error('Network error:', error);
    }
  }

  async function checkLoginStatus() {
    try {
      const response = await fetch('/api/check-login-status', { // エンドポイントのURLを確認
        headers: {
          'Authorization': `Bearer ${token}`, // tokenはサーバーから取得した認証トークン
          'Accept': 'application/json',
        }
      });

      if (!response.ok) {
        if (response.status === 401) {
          window.location.href = '/login'; // ログインページにリダイレクト
          return false;
        }
        throw new Error('Network response was not ok');
      }

      const data = await response.json();
      return data.isLoggedIn;
    } catch (error) {
      console.error('Failed to check login status:', error);
      return false;
    }
  }
});
