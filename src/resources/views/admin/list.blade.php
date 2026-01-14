@extends('admin.layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('css/list.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="head-title">
        <h1>　{{ $target->format('Y年m月d日') }}の勤怠</h1>
    </div>

    <div class="month-select">
        <div class="previous-month">
        <a href="{{route('admin.attendance.list', ['day' => $previousDay->format('Y-m-d')])}}">
            <img src="{{asset('/images/left-direction.png')}}" class="direction-logo">
            <p>前 日</p>
        </a>
        </div>
        <div class="current-month">
            <img src="{{asset('/images/calender.png')}}" class="calender-logo">
            <p>{{ $target->format('Y/m/d') }}</p>
        </div>
        <div class="next-month">
        <a href="{{route('admin.attendance.list', ['day' => $nextDay->format('Y-m-d')])}}">
            <p>翌 日</p>
            <img src="{{asset('/images/right-direction.png')}}" class="direction-logo" alt="">
        </a>
        </div>
    </div>

<table class="attend-table">
    <thead>
    <tr>
        <th>名前</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>休憩</th>
        <th>合計</th>
        <th>詳細</th>
    </tr>
    </thead>    
    <tbody>
        @foreach($attendances as $attendance)
        @php
            $change = $attendance->changeAttendance ?? null;
        @endphp
        <tr>
            <td>{{ optional($attendance)->user->name }}</td>
        @if ($attendance && $attendance->clock_in_time)
            <td>{{ optional($attendance)->clock_in_time->format('H:i') }}</td>
            <td>{{ optional($attendance)->clock_out_time->format('H:i') }}</td>
        @elseif($change && $change->requested_clock_in_time)
            <td>{{ substr($change->requested_clock_in_time,0,5) }}</td>
            <td>{{ substr($change->requested_clock_out_time,0,5) }}</td>
        @else
                <td></td>
                <td></td>
        @endif
            
        @if ($attendance && $attendance->calcBreakMinutes() > 0)
            <td>{{ gmdate('H:i', $attendance->calcBreakMinutes() * 60) }}</td>
        @elseif($change && $change ->breakMinutes() >0)
            <td>{{ gmdate('H:i', $change->breakMinutes()* 60 )}}</td> 
        @else
            <td></td>
        @endif

        @if ($attendance && $attendance->calcWorkMinutes() > 0)
            <td>{{ gmdate('H:i', $attendance->calcWorkMinutes() * 60) }}</td>
        @elseif( $change && $change->netWorkMinutes()>0)
            <td>{{ gmdate('H:i',$change->netWorkMinutes()*60)}}</td>
        @else
            <td></td>
        @endif
            <td>
                @if ($attendance)
                <a href="{{ route('admin.attendance.detail', ['id' => $attendance->id,'user_id'=>$attendance->user->id]) }}" class="detail-link">詳細</a>
                @else
                <a href="{{ route('admin.attendance.detail', ['id' => 0, 'user_id' => $user->id, 'work_date' => $target->toDateString()]) }}" class="detail-link">詳細</a>
                @endif  
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection