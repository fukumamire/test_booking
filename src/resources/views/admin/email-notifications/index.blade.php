@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_email_notification.css') }}">
@endsection
@section('content')
<body>
    <div class="container">
        <h2 class="page-title">お知らせメール作成</h2>

        @if ($errors->any())
            <div class="alert error-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.email-notification.store') }}" method="POST" class="form-wrapper">
            @csrf
            
            <div class="form-group">
                <label for="subject" class="form-label">件名:</label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>

            <div class="form-group">
                <label for="content" class="form-label">本文:</label>
                <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <label for="recipients" class="form-label">宛先:</label>
                <input type="email" class="form-control" id="recipients" name="recipients" required>
            </div>

            <div class="submit-button-wrapper">
                <button type="submit" class="btn submit-button">送信</button>
            </div>
        </form>
    </div>
</body>
@endsection