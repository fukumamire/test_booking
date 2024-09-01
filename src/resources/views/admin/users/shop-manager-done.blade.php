@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/done.css') }}">
@endsection

@section('content')
<body class="done-page">
    <div class="content__wrap">
        <p class="content__text">
            {{ session('success') }}
        </p>
        <a class="content__button" href="/">戻る</a>
    </div>
</body>
@endsection