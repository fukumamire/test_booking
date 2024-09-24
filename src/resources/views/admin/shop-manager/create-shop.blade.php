@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/shop-manager/create-shop.css') }}">
@endsection

@section('content')
<div class="create-shop-container">
    <h1 class="create-shop-title">新規店舗作成</h1>
    <form action="{{ route('shop-manager.shops.store') }}" method="POST" class="create-shop-form">
        @csrf
        <div class="form-group">
            <label for="name" class="form-label">店舗名</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label for="areas" class="form-label">エリア</label>
            <select id="areas" name="area_ids[]" class="form-select" multiple required>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="genres" class="form-label">ジャンル</label>
            <select id="genres" name="genre_ids[]" class="form-select" multiple required>
                @foreach($genres as $genre)
                    <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="outline" class="form-label">概要</label>
            <textarea id="outline" name="outline" class="form-textarea" required></textarea>
        </div>
        <button type="submit" class="submit-button">作成</button>
    </form>
</div>
@endsection