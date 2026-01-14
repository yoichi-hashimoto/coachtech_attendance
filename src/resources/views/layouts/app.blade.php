<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    @yield('css')
</head>
<body>
<header class="header">
<a href="">
    <img src="{{asset('images/COACHTECHヘッダーロゴ.png')}}" alt="ヘッダーロゴ" class="header-logo">
</a>
@auth
@if(auth()->user()->role === 'admin')
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
@else
<nav>
    <ul class="header-links">
        <li><a href="{{route('attendance')}}">勤怠</a></li>
        <li><a href="{{route('attendance.list')}}">勤怠一覧</a></li>
        <li><a href="{{route('stamp_correction_request')}}">申請</a></li>
        @auth
        <li><form action="{{route('logout')}}" method="post">
                @csrf
            <button type="submit" class="header-links">ログアウト</button>
            </form>
        @endauth
        </li>
    </ul>
</nav>
@endif
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