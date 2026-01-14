@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/register.css')}}">
@endsection

@section('content')
<body>
<form action="{{ route('register')}}" method="POST">
    @csrf
    <div class="register-form">
        <div class="register-title">
            <h1>会員登録</h1>
        </div>
    <h3>名前
        <input type="text" name="name" value="{{old('name')}}" class="input-form">
    </h3>
        @error('name')
        <p>{{( $message )}}</p>
        @enderror
    <h3>メールアドレス
        <input type="text" name="email" value="{{old('email')}}" class="input-form">
    </h3>
        @error('email')
        <p>{{( $message )}}</p>
        @enderror
    <h3>パスワード
        <input type="password" name="password" value="" class="input-form">
    </h3>
        @error('password')
        <p>{{( $message )}}</p>
        @enderror
    <h3>パスワード確認
        <input type="password" name="password_confirmation" value="" class="input-form">
    </h3>
    <button type="submit" class="submit-button">
        登録する
    </button>
    </div>
</form>
    <div class="login-route">
        <a href="{{asset('/login')}}" class="login-link">ログインはこちら</a>
    </div>


</body>
</html>

@endsection