@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/list.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="head-title">
        <h1>　スタッフ一覧</h1>
    </div>

<table class="attend-table">
    <thead>
    <tr>
        <th>名前</th>
        <th>メールアドレス</th>
        <th>月次勤怠</th>
    </tr>
    </thead>    
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><a href="{{route('admin.attendance.staff', ['id' => $user->id,])}}" class="detail-link">詳細</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection