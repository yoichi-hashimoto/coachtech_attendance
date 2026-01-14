    @extends('layouts.app')
    @section('css')
    <link rel="stylesheet" href="{{ asset('css/application.css') }}">
    @endsection

    @section('content')
    <div class="staff-page">
        <div class="head-title">
            <h1>　申請一覧</h1>
        </div>
        
    @php
        $isPending = ($status ?? 'pending') === 'pending';
    @endphp

    @auth
    @if(auth()->user()->role === 'admin')
        <div class="approve-status">
        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'pending']) }}" class="{{ $isPending ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('admin.stamp_correction_request.list', ['status' => 'approved']) }}" class="{{ !$isPending ? 'active' : '' }}">承認済み</a>
    </div>
    <table class="staff-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allApplications as $all)
            <tr>
                <td>{{ $all->approval_status }}</td>
                <td>{{ $all->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($all->work_date)->format('Y/m/d') }}</td>
                <td>{{ $all->notes }}</td>
                <td>{{ optional($all->created_at)->format('Y/m/d') }}</td>
                <td><a class="detail-link" href="{{ route('admin.stamp_correction_request.detail', [
        'attendance_correct_request_id' => $all->id,
        'work_date' => \Carbon\Carbon::parse($all->work_date)->toDateString(),'user_id'=>$all->user_id,
    ]) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else

    <div class="approve-status">
        <a href="{{ route('stamp_correction_request', ['status' => 'pending']) }}" class="{{ $isPending ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('stamp_correction_request', ['status' => 'approved']) }}" class="{{ !$isPending ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="staff-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usersAttendances as $usersAttendance)
            <tr>
                <td>{{ $usersAttendance->approval_status }}</td>
                <td>{{ $usersAttendance->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($usersAttendance->work_date)->format('Y/m/d') }}</td>
                <td>{{ $usersAttendance->notes }}</td>
                <td>{{ optional($usersAttendance->created_at)->format('Y/m/d') }}</td>
                <td><a class="detail-link" href="{{ route('attendance.detail', [
        'id' => $usersAttendance->attendance_id ?? 0,
        'work_date' => \Carbon\Carbon::parse($usersAttendance->work_date)->toDateString(),
    ]) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endauth
    </div>
    @endsection