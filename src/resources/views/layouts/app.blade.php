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
  <header class="header-left">
    {{-- <div class="header-left-content"> --}}
      <input type="checkbox" id="overlay-input" class="overlay-toggle" />
      <label for="overlay-input" id="overlay-button" class="overlay-button"><span></span></label>
      <div class="header__logo">Rese</div>
    {{-- </div> --}}
    <div id="overlay" class="overlay-menu">
      <ul class="nav__list">
        @auth('admin')
        <!-- 管理者ログイン時のメニュー -->
          <li><a href="{{ route('shops.index') }}" class="nav-link">Home</a></li>
          <li>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button type="submit" class="logout-link">Logout</button>
            </form>
          </li>
          <li><a href="{{ route('admin.index') }}" class="nav-link">Admin only page～管理者専用～</a></li>
        @endauth

        @auth('web')
          <!-- 一般ユーザーログイン時のメニュー -->
          <li><a href="{{ route('shops.index') }}" class="nav-link">Home</a></li>
          <li><a href="{{ route('mypage') }}" class="nav-link">My Page</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="logout-link">Logout</button>
            </form>
          </li>
        @endauth

        @auth('shop_manager')
          <!-- 店舗管理者ログイン時のメニュー -->
          <li><a href="{{ route('shops.index') }}" class="nav-link">Home</a></li>
          <li><a href="{{ route('shop-manager.dashboard') }}" class="nav-link">Dashboard～店舗管理者専用～</a></li>
          <li>
            <form method="POST" action="{{ route('shop-manager.logout') }}">
              @csrf
              <button type="submit" class="logout-link">Logout</button>
            </form>
          </li>
        @endauth

        @guest
          <!-- ゲスト（ログインしていない）時のメニュー -->
          <li><a href="{{ route('shops.index') }}" class="nav-link">Home</a></li>
          <li><a href="{{ route('register') }}" class="nav-link">Registration</a></li>
          <li><a href="{{ route('login') }}" class="nav-link">Login</a></li>
          <li><a href="{{ route('admin.login') }}" class="nav-link">Admin Login</a></li>
          <li><a href="{{ route('shop-manager.login') }}" class="nav-link">Shop Manager Login</a></li>
        @endguest
      </ul>
    </div>
    @yield('header')
  </header>
  <main>
      @yield('content')
  </main>

    @yield('script')
</body>
</html>