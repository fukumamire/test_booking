function toggleFavorite(button, shopId) {
  fetch(`/shops/${shopId}/toggle-favorite`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
  })
    .then(response => {
      if (response.status === 401) { // 未ログインの場合、ログインページへリダイレクト
        return response.json().then(data => {
          window.location.href = data.redirect;
        });
      } else {
        return response.json(); // ログインしている場合は通常通り処理を続行
      }
    })
    .then(data => {
      if (data.success) {
        button.classList.remove('favorite', 'not-favorite'); // 既存のクラスを削除
        if (data.is_favorite) {
          button.classList.add('favorite'); // お気に入りに追加されたら favorite クラスを追加
        } else {
          button.classList.add('not-favorite'); // お気に入りから削除されたら not-favorite クラスを追加
        }
      }
    })
    .catch(error => console.error('Error:', error));
}