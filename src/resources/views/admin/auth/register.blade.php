@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<body class="register-page">
  <div class="registration-container">
    <div class="registration-header">
      <h2>Administrator register</h2>
    </div>
    <form action="{{ route('admin.register.submit') }}" method="post" class="registration-form">
      @csrf
      <div class="form-group">
        <img src="{{ asset('auth-img/male.png') }}" alt="userアイコン" width="30">
        <input type="text" id="username" name="name" placeholder="username" value="{{ old('name') }}">
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <img src="{{ asset('auth-img/mail.png') }}" alt=",mailアイコン"  width="30">
        <input type="email" id="email" name="email" placeholder="email" value="{{ old('email') }}">
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <img src="{{ asset('auth-img/key.png') }}" alt="keyアイコン" width="30">
        <input type="password" id="password" name="password" placeholder="パスワード">
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <img src="{{ asset('auth-img/key.png') }}" alt="keyアイコン" width="30">
        <input type="password" id="confirm_password" name="password_confirmation" placeholder="確認用パスワード">
        @error('password_confirmation')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <!-- メール認証に関するメッセージを追加 -->	
      {{-- <p>登録後、自動的に送信されるメールから本人確認が必要です。</p> --}}
      <div class="form-submit">
        <button type="submit" class="submit-button">登録</button>
      </div>
    </form>
  </div>
</body>
@endsection
