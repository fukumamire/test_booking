@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/app.css') }}"> 
<link rel="stylesheet" href="{{ asset('css/register.css') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

@endsection
@section('content')
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
@endsection