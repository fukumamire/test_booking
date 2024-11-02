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
        <p>{{ $user['name'] }}様</p>
        <p>ご登録いただき、誠にありがとうございます。</p>
        <p>以下のボタンをクリックして、メールアドレスの確認をお願いします。</p>
        <p><a href="{{ route('verification.verify', ['id' => $user['id'], 'hash' => $token]) }}" class="button">メールアドレスの確認</a></p>
        <p>このメールに心あたりがない場合は、このメールは無視してください。</p>
        <p>{{ config('app.name') }}</p>
    </div>
</body>

</html>