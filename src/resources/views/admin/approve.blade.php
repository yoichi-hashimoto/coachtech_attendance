@extends('admin.layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')



<div class="attend-page">
<div class="head-title">
    <h1>　勤怠詳細</h1>
</div>

@php
$workDate = $change->work_date;
$date = Carbon\Carbon::parse($workDate);
$changeRests = $change->changeRests;
@endphp

<table class="attend-table">
    <tbody>
    <tr>
        <th>名前</th>
        <td>{{ $change->user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{$date->format('Y')}}年</td>
        <td></td>
        <td>{{$date->format('m')}}月{{$date->format('d') }}日</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td>{{ substr($change->requested_clock_in_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($change->requested_clock_out_time,0,5) }}</td>
        <td>@error('requested_clock_out_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @foreach($changeRests as $changeRest)
    <tr>
        <th>休憩</th>
        <td>{{ substr($changeRest->requested_break_start_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($changeRest->requested_break_end_time,0,5)     }}</td>
        <td></td>
        <td></td>
    </tr>
        @endforeach
    <tr>
        <th>備考</th>
        <td colspan="3">{{ $change->notes }}</textarea></td>
        <td>@error('notes')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    </tbody>
</table>

@php
  $approvalStatus = $change->approval_status ?? null; // '承認待ち' or '承認済み'
@endphp

@if($approvalStatus === '承認待ち')
    <form action="{{ route('admin.stamp_correction_request.approve', ['attendance_correct_request_id' => $change->id]) }}" method="POST">
        @csrf   
    <div class="edit-area">
        <button class="edit-button" type="submit">承認</button>
    </div>
    </form>
@else
    <div class="edit-area">
        <button class="edit-button__disabled" type="button" disabled>承認済み</button>
    </div>
@endif