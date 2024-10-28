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
        <p>登録したメールアドレスに確認メールを送信しました。メールに記載されたリンクをクリックして、メールアドレスを確認してください。</p>
        <p>もしメールが届いていない場合は、<a href="{{ route('verification.resend') }}">再送信</a>を試みてください。</p>
    </div>
</body>
</html>