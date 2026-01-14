<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('/css/app.css')}}">
    @yield('css')
</head>
<body>
<header class="header">
<a href="">
    <img src="{{asset('images/COACHTECHヘッダーロゴ.png')}}" alt="ヘッダーロゴ" class="header-logo">
</a>
@auth
<nav>
    <ul class="header-links">
        <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
        <li><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
        <li><a href="{{ route('admin.stamp_correction_request.list') }}">申請一覧</a></li>
        <li>
            <form action="{{ route('logout')}}" method="post">
                @csrf
                <button type="submit" class="header-links">ログアウト</button>
            </form>
        </li>
    </ul>
</nav>
@endauth

</header>
<main>
    @if(session('message'))
    <p>{{session('message')}}</p>
    @endif
    @yield ('content')
</main>

</body>
</html>