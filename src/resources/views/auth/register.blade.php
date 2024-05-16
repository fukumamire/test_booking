@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<body class="register-page">
  <div class="registration-container">
    <div class="registration-header">
      <h2>Registration</h2>
    </div>
    <form action="/register" method="post" class="registration-form">
      @csrf
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="name" placeholder="username" value="{{ old('name') }}">
        @error('name')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="email" value="{{ old('email') }}">
        @error('email')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="password">
        @error('password')
          <div class="error-message">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-submit">
        <button type="submit" class="submit-button">登録</button>
      </div>
    </form>
  </div>
</body>
@endsection
