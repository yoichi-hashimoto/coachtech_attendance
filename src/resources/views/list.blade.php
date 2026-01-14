@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('/css/list.css')}}">
@endsection

@section('content')
<div class="attendance-list">
    <div class="head-title">
        <h1>　勤怠一覧</h1>
    </div>

    <div class="month-select">
        <div class="previous-month">
        <a href="{{route('attendance.list', ['month'=>$prevMonth])}}">
            <img src="{{asset('/images/left-direction.png')}}" class="direction-logo">
            <p>前 月</p>
        </a>
        </div>
        <div class="current-month">
            <img src="{{asset('/images/calender.png')}}" class="calender-logo">
            <p>{{ $thismonth }}</p>
        </div>
        <div class="next-month">
        <a href="{{route('attendance.list', ['month'=>$nextMonth])}}">
            <p>翌 月</p>
            <img src="{{asset('/images/right-direction.png')}}" class="direction-logo" alt="">
        </a>
        </div>
    </div>

<table class="attend-table">
    <thead>
    <tr>
        <th>日付</th>
        <th>出勤</th>
        <th>退勤</th>
        <th>休憩</th>
        <th>合計</th>
        <th>詳細</th>
    </tr>
    </thead>    
    <tbody>
        @foreach($days as $day)
        @php
            $attendance = $day['attendance'];
            $change = $day['latestChange'] ?? null;

            $reqIn = $change ? $change->requested_clock_in_time : null;
            $reqOut = $change ? $change->requested_clock_out_time : null;
        @endphp

        <tr>
            <td>{{ $day['date']->isoFormat('MM/DD/(ddd)') }}</td>
            @if($change && $change->requested_clock_in_time)
                <td>{{ \Carbon\Carbon::parse($reqIn)->format('H:i')}}</td>
                <td>{{ $reqOut ? \Carbon\Carbon::parse($reqOut)->format('H:i') : ''}}</td>
            @elseif ($day['attendance'] && $day['attendance']->clock_in_time)
                <td>{{ $day['attendance']?->clock_in_time?->format('H:i') ?? '' }}</td>
                <td>{{ $day['attendance']?->clock_out_time?->format('H:i') ?? '' }}</td>

            @else
                <td></td>
                <td></td>
                @endif
            <td>
                @if($change && $change->breakMinutes() > 0)
                    {{ gmdate('H:i', $change->breakMinutes()*60)}}
                @elseif ($day['attendance'] && $day['attendance']->calcBreakMinutes() > 0)
                    {{ gmdate('H:i', $day['attendance']->calcBreakMinutes() * 60) }}
                @else
                    
                @endif
            </td>
            <td>
                @if ($day['attendance'] && $day['attendance']->calcWorkMinutes() > 0)
                    {{ gmdate('H:i', $day['attendance']->calcWorkMinutes() * 60) }}
                @elseif($change && $change->netWorkMinutes() > 0)
                    {{ gmdate('H:i', $change->netWorkMinutes()*60)}}
                @else
                    
                @endif
            </td>
            <td>
                @if ($day['attendance'])
                <a href="{{ route('attendance.detail', ['id' => $day['attendance']->id]) }}" class="detail-link">詳細</a>
                @else
                <a href="{{ route('attendance.detail', ['id' => 0, 'work_date' => $day['date']->toDateString()]) }}" class="detail-link">詳細</a>
                @endif  
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endsection