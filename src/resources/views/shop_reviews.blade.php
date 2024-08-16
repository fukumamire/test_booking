@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/shop_reviews.css') }}">
@endsection

@section('content')
<div class="reviews-container">
    <h2>{{ $shop->name }} のレビュー</h2>

    @foreach ($reviews as $review)
        <div class="review-item">
            <p><strong>タイトル:</strong> {{ $review->title }}</p>
            <p><strong>評価:</strong> {{ $review->rating }}/5</p>
            <p><strong>コメント:</strong> {{ $review->comment }}</p>
            <hr>
        </div>
    @endforeach
</div>
@endsection