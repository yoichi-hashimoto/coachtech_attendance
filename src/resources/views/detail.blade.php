@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')

<div class="attend-page">
<div class="head-title">
    <h1>　勤怠詳細</h1>
</div>

@php
    $isPending = optional($latestChange)->approval_status === '承認待ち';
    $isApproved = optional($latestChange)->approval_status === '承認済み';
    $workDateObj = \Carbon\Carbon::parse($workDate ?? $date);
@endphp

@if($isPending)
<table class="attend-table">
    <tbody>
    <tr>
        <th>名前</th>
        <td>{{ $user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{$workDateObj->format('Y')}}年</td>
        <td></td>
        <td>{{$workDateObj->format('m')}}月{{$workDateObj->format('d') }}日</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td>{{ substr($latestChange->requested_clock_in_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($latestChange->requested_clock_out_time,0,5) }}</td>
        <td>@error('requested_clock_out_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @foreach($restChanges as $restChange)
    <tr>
        <th>休憩</th>
        <td>{{ substr($restChange->requested_break_start_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($restChange->requested_break_end_time,0,5)     }}</td>
        <td></td>
        <td></td>
    </tr>
        @endforeach
    <tr>
        <th>備考</th>
        <td colspan="3">{{ $latestChange->notes }}</textarea></td>
        <td>@error('notes')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    </tbody>
</table>
<div class="edit-area">
    <p class="edit-button__disabled" type="button" disabled>*承認待ちのため修正はできません</p>
</div>

@elseif(is_null($attendance))
<form action="{{ route('stamp_correction_request.store') }}" method="POST">
    @csrf
    <input type="hidden" name="work_date" value="{{ $workDateObj->toDateString() }}">
<table class="attend-table">
    <tbody>
    <tr>
        <th>名前</th>
        <td>{{ $user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{$workDateObj->format('Y')}}年</td>
        <td></td>
        <td>{{$workDateObj->format('m')}}月{{$workDateObj->format('d') }}日</td>
        <td><input type="hidden" name="work_date" value="{{ $workDateObj->toDateString() }}"></td>
        <td></td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td><input name="requested_clock_in_time" type="text"></td>
        <td>～</td>
        <td><input name="requested_clock_out_time" type="text" ></td>
        <td>@error('requested_clock_out_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    @if($rests->isEmpty())
    <tr>
        <th>休憩</th>
        <td><input name="requested_break_start_time" type="text"></td>
        <td>～</td>
        <td><input name="requested_break_end_time" type="text"></td>
        <td>@error('requested_break_start_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td>@error('requested_break_end_time')<span class="error">{{ $message }}</span>@enderror</td>
    @else
        @foreach($rests as $rest)
    <tr>
        <th>休憩</th>
        <td><input name="requested_break_start_time" type="text" ></td>
        <td>～</td>
        <td><input name="requested_break_end_time" type="text" ></td>
        <td>@error('requested_break_end_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @endforeach
        @endif
    <tr>
        <th>備考</th>
        <td colspan="3"><textarea name="notes" id=""></textarea></td>
        <td>@error('notes')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    </tbody>
</table>
<div class="edit-area">
    <button class="edit-button" type="submit">修正</button>
</div>
</form>

@elseif($isApproved)
<table class="attend-table">
    <tbody>
    <tr>
        <th>名前</th>
        <td>{{ $user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{$workDateObj->format('Y')}}年</td>
        <td></td>
        <td>{{$workDateObj->format('m')}}月{{$workDateObj->format('d') }}日</td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td>{{ substr($latestChange->requested_clock_in_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($latestChange->requested_clock_out_time,0,5) }}</td>
        <td>@error('clock_out_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @foreach($restChanges as $restChange)
    <tr>
        <th>休憩</th>
        <td>{{ substr($restChange->requested_break_start_time,0,5) }}</td>
        <td>～</td>
        <td>{{ substr($restChange->requested_break_end_time,0,5) }}</td>
        <td></td>
        <td></td>
    </tr>
        @endforeach
    <tr>
        <th>備考</th>
        <td colspan="3">{{ $latestChange->notes }}</textarea></td>
        <td>@error('notes')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    </tbody>
</table>
<div class="edit-area">
    <button class="edit-button__disabled" type="button" disabled>承認済み</button>
</div>

@else
<form action="{{ route('stamp_correction_request.store', ['id' => optional($attendance)->id ?? 0]) }}" method="POST">
    @csrf
    <input type="hidden" name="work_date" value="{{ $workDateObj->toDateString() }}">
<table class="attend-table">
    <tbody>
    <tr>
        <th>名前</th>
        <td>{{ $user->name }}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <th>日付</th>
        <td>{{$workDateObj->format('Y')}}年</td>
        <td></td>
        <td>{{$workDateObj->format('m')}}月{{$workDateObj->format('d') }}日</td>
        <td><input type="hidden" name="work_date" value="{{ $workDateObj->toDateString() }}"></td>
        <td></td>
    </tr>
    <tr>
        <th>出勤・退勤</th>
        <td><input name="requested_clock_in_time" type="text" value="{{ old('requested_clock_in_time', optional($attendance?->clock_in_time)->format('H:i')) }}"></td>
        <td>～</td>
        <td><input name="requested_clock_out_time" type="text" value="{{ old('requested_clock_out_time', optional($attendance?->clock_out_time)->format('H:i')) }}"></td>
        <td>@error('requested_clock_out_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @foreach($rests as $rest)
    <tr>
        <th>休憩</th>
        <td><input name="requested_break_start_time" type="text" value="{{ old('requested_break_start_time', substr($rest->break_start_time,0,5)) }}"></td>
        <td>～</td>
        <td><input name="requested_break_end_time" type="text" value="{{ old('requested_break_end_time', substr($rest->break_end_time,0,5)) }}"></td>
        <td>@error('requested_break_end_time')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
        @endforeach
    <tr>
        <th>備考</th>
        <td colspan="3"><textarea name="notes" id="">{{ $attendance?->notes }}</textarea></td>
        <td>@error('notes')<span class="error">{{ $message }}</span>@enderror</td>
        <td></td>
    </tr>
    </tbody>
</table>
<div class="edit-area">
    <button class="edit-button" type="submit">修正</button>
</div>
</form>
@endif
</div>
@endsection
