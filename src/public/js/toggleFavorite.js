async function toggleFavorite(button, shopId) {
  const isLoggedIn = document.querySelector('meta[name="login-status"]').getAttribute('content') === 'true';
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  if (!isLoggedIn) {
    window.location.href = '/request_login';
    return;
  }

  const url = `/api/shops/${shopId}/toggle-favorite`;
  const method = 'POST';

  try {
    const response = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        'X-CSRF-TOKEN': token,
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
