<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <title>Rese</title>
@yield('css')
</head>

<body>
  <header>
    <input type="checkbox" id="overlay-input" />
    <label for="overlay-input" id="overlay-button"><span></span></label>
    <div id="overlay">
    <ul>
      @if(Auth::check())
        <!-- ログインしている場合のメニュー -->
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('logout') }}">Logout</a></li>
        <li><a href="{{ route('mypage') }}">Mypage</a></li>
      @else
        <!-- ログインしていない場合のメニュー -->
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('register') }}">Registration</a></li>
        <li><a href="{{ route('login') }}">Login</a></li>
      @endif
      </ul>
    </div>
  </header>


</body>
</html>