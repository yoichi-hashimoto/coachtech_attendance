@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{asset('/css/attendance.css')}}">
@endsection

@section('content')
@switch( $status )
@case( null )
<form action="{{route('attendance.store')}}" method="POST">
    @csrf
    <div class="attendance-items">
        <div class="work-status">勤務外</div>
        <div class="current-date">
            {{ $today->isoFormat('Y年MM月DD日ddd曜日') }}
            <input type="date" name="work_date" value="{{ $today->format('Y-m-d') }}" hidden>
        </div>
        <div class="current-time">{{ $now->format('H:i') }}</div>
            <input type="time" name="clock_in_time" value="{{ $now->format('H:i') }}" hidden>
        <div class="button-wrap">
            <button type="submit" class="work-button">出勤</button>
        </div>
    </div>
</form>
    @break
@case('出勤中')
<form action="{{route('attendance.update')}}" method="POST">
    @csrf
    @method('PATCH')
    <div class="attendance-items">
        <div class="work-status">{{( $status )}}</div>
        <div class="current-date">
            {{ $today->isoFormat('Y年MM月DD日ddd曜日') }}
            <input type="date" name="work_date" value="{{ $today->format('Y-m-d') }}" hidden>
        </div>
        <div class="current-time">{{ $now->format('H:i') }}</div>
            <!-- <input type="time" name="clock_in_time" value="{{ $now->format('H:i') }}" hidden> -->
    <div class="attendance-buttons">
        <div class="button-wrap">
            <button type="submit" class="work-button" name="action" value="clock_out">退勤</button>
                <input type="hidden" name="clock_out_time" value="{{ $now->format('H:i') }}">
        </div>
        <div class="button-wrap">
            <button type="submit" class="rest-button" name="action" value="break_start">休憩入</button>
               <input type="hidden" name="break_start_time" value="{{ $now->format('H:i') }}">   
        </div>
    </div>
    </div>
</form>
    @break
@case('休憩中')
<form action="{{route('attendance.update')}}" method="POST">
    @csrf
    @method('PATCH')
    <div class="attendance-items">
        <div class="work-status">{{($status)}}</div>
        <div class="current-date">
            {{ $today->isoFormat('Y年MM月DD日ddd曜日') }}
            <input type="date" name="work_date" value="{{ $today->format('Y-m-d') }}" hidden>
        </div>
        <div class="current-time">{{ $now->format('H:i') }}</div>
            <!-- <input type="time" name="break_end" value="{{ $now->format('H:i') }}" hidden> -->
        <div class="button-wrap">
            <button type="submit" class="work-button" name="action" value="break_end">休憩戻</button>
            <input type="hidden" name="break_end_time" value="{{ $now->format('H:i') }}">
        </div>
    </div>
</form>
    @break
@case('退勤済')
<!-- <form action="{{route('attendance.update')}}" method="POST"> -->
    @csrf
    <div class="attendance-items">
        <div class="work-status">{{($status)}}</div>
        <div class="current-date">
            {{ $today->isoFormat('Y年MM月DD日ddd曜日') }}
            <!-- <input type="date" name="work_date" value="{{ $today->isoformat('YYYY/MM/DD') }}" hidden> -->
        </div>
        <div class="current-time">{{ $now->format('H:i') }}</div>
            <!-- <input type="time" name="clock_in_time" value="{{ $now->format('H:i') }}" hidden> -->
        <div class="button-wrap">
            <h1>お疲れさまでした。</h1>
        </div>
    </div>
<!-- </form> -->
    @break
    @endswitch
@endsection