@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/authmail.css')}}">
@endsection

@section('content')
<body>
<div class="authmail-form">
    <h1 class="auth-text">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</h1>
        <div class="auth-submit">
            <a href="{{ asset('http://localhost:8025/') }}">
            <button type="submit" class="submit-button">
                認証はこちらから
            </button>
            </a>    
        </div>
        <div class="authmail-route">
        <form action ="{{route('verification.send')}}" method ="POST">
            @csrf
                <button class="authmail-link">認証メールを再送する</button>
        </form>
    </div>
</div>
</body>
</html>

@endsection