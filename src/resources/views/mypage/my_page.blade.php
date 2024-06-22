@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection
@section('content')
<div class="mypage-container">
    <h1>マイページ</h1>
    <p>ようこそ、さん</p>
    <p>あなたのメールアドレス: </p>
    {{-- ユーザーの予約情報などを表示 --}}
</div>
@endsection