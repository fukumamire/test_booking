<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>メールアドレスの確認 | {{ config('app.name', 'Laravel') }}</title>
  <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
  <div class="container">
    <h1>メールアドレスの確認</h1>
    <div class="card">
      <div class="card-body">
        <p>ご登録いただき、誠にありがとうございます。</p>
        <p>以下のボタンをクリックして、メールアドレスの確認をお願いします。</p>

        @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-4">
          新しい確認リンクが、あなたが登録したメールアドレスに送信されました。
        </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
          @csrf

          <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary">
              確認メールを再送信
            </button>
          </div>
        </form>

        <hr>

        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <button type="submit" class="btn btn-outline-secondary w-100">
            ログアウト
          </button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>