async function toggleFavorite(button, shopId) {
  const isLoggedIn = await checkLoginStatus(); // ユーザーがログインしているかどうかをチェック

  if (!isLoggedIn) {
    window.location.href = '/login'; // ログインページへのパスに変更
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

    if (!response.ok) { // レスポンスがOKでない場合（例: 401 Unauthorized）
      if (response.status === 401) {
        window.location.href = '/login'; // ログインページにリダイレクト
        return;
      }
      throw new Error('Network response was not ok');
    }

    const data = await response.json();

    if (data.success) {
      // UIの更新処理
      button.classList.toggle('heart-active'); // ここではクラス名を適宜変更
    } else {
      console.error('Error toggling favorite:', data.error);
    }
  } catch (error) {
    console.error('Network error:', error);
  }
}

async function checkLoginStatus() {
  try {
    const response = await fetch('/api/check-login-status', {
      headers: {
        'Authorization': `Bearer ${token}`, // tokenはサーバーから取得した認証トークン
        'Accept': 'application/json',
      }
    });

    if (!response.ok) {
      if (response.status === 401) {
        return false; // ログインしていない場合
      } else {
        throw new Error('Network response was not ok');
      }
    }

    const data = await response.json();
    return data.isLoggedIn;
  } catch (error) {
    console.error('Failed to check login status:', error);
    return false;
  }
}



// async function toggleFavorite(button, shopId) {
//   const isLoggedIn = await checkLoginStatus(); // ユーザーがログインしているかどうかを判定

//   if (!isLoggedIn) {
//     window.location.href = '/request_login'; // ログインしていない場合は会員登録　ログインへリダイレクト
//     return;
//   }

//   const url = `/shops/${shopId}/toggle-favorite`; // 常にこのURLを使用
//   const method = 'POST'; // メソッドはPOST固定

//   try {
//     const response = await fetch(url, {
//       method,
//       headers: {
//         'Content-Type': 'application/json',
//         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//       },
//     });

//     const data = await response.json();

//     if (data.success) {
//       // UIの更新処理
//       // 例: ボタンのクラスを切り替えるなど
//       button.classList.toggle('heart-active'); // ここではクラス名を適宜変更
//     } else {
//       console.error('Error toggling favorite:', data.error);
//     }
//   } catch (error) {
//     console.error('Network error:', error);
//   }
// }

// async function checkLoginStatus() {
//   const response = await fetch('/api/check-login-status');
//   const data = await response.json();
//   return data.isLoggedIn;
// }