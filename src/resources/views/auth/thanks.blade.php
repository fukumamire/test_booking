@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('content')
<body class="thanks-page">
    <div class="content__wrap">
        <p class="content__text">
            会員登録ありがとうございます
        </p>
        <p>登録後、自動的に送信されるメールから本人確認をしてください。</p>
        <a class="content__button" href="{{ route('login') }} ">ログインする</a>
    </div>
</body>
@endsection