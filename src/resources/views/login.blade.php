@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/login.css')}}">
@endsection

@section('content')
<form action="{{route('login')}}" method="post">
        @csrf
    <div class="login-form">
        <div class="login-title">
            <h1>ログイン</h1>
        </div>
    <h3>メールアドレス
        <input type="text" name="email" value="{{old('email')}}" class="input-form">
        @error('email')
        <p>{{( $message )}}</p>
        @enderror
    </h3>
    <h3>パスワード
        <input type="password" name="password" value="{{old('password')}}" class="input-form">
        @error('password')
        <p>{{( $message )}}</p>
        @enderror
    </h3>
    <button type="submit" class="submit-button">
        ログインする
    </button>
    </div>
</form>
    <div class="login-route">
        <a href="{{route('register')}}" class="login-link">会員登録はこちら</a>
    </div>

@endsection