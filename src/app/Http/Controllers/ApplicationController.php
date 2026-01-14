<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApplicationRequest;
use App\Models\Attendance;
use App\Models\ChangeAttendance;
use App\Models\ChangeRest;
use App\Models\User;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function showList(Request $request){
        $user = auth()->user();
        
        $status = $request->query('status','pending');

        $usersAttendances = ChangeAttendance::with('attendance','user')
            ->where('user_id',$user->id)
            ->where('approval_status',$status === 'approved' ? '承認済み' : '承認待ち')
            ->orderBy('created_at','desc')
            ->get();

        $allApplications = ChangeAttendance::with('attendance','user','changeRests')
            ->where('approval_status',$status === 'approved' ? '承認済み' : '承認待ち')
            ->orderBy('created_at','desc')
            ->get();

        return view ('application',compact('status','allApplications','usersAttendances'));
    }

    public function store(ApplicationRequest $request){
        $user = auth()->user();
        $validated = $request->validated();
        $workDate = Carbon::parse($request->input('work_date'))->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['user_id'=>$user->id, 'work_date'=>$workDate],['work_status'=>'退勤済'])
            ->load('rests');

        $changeAttendance = ChangeAttendance::create([
            'attendance_id' => $attendance?->id,
            'user_id' => $user->id,
            'work_date' => $workDate,
            'requested_clock_in_time' => $validated['requested_clock_in_time'],
            'requested_clock_out_time' => $validated['requested_clock_out_time'],
            'notes' => $validated['notes'] ?? null,
            'approval_status' => '承認待ち',
        ]);
        
        $breakStart = $validated['requested_break_start_time'] ?? null;
        $breakEnd   = $validated['requested_break_end_time'] ?? null;

        if ($breakStart || $breakEnd){
            $rest =  $attendance && $attendance->rests->isNotEmpty()
            ? $attendance->rests->first()
            : null;
            ChangeRest::create([
                'change_attendance_id' => $changeAttendance->id,
                'rest_id' => $rest?->id,
                'requested_break_start_time' => $validated['requested_break_start_time'] ?? null,
                'requested_break_end_time' => $validated['requested_break_end_time'] ?? null,
                'approval_status' => '承認待ち',
            ]);
    }
        return redirect()->route('stamp_correction_request')->with('message', '申請が完了しました');
    }

    public function storeForAdmin(ApplicationRequest $request){
        $validated = $request->validated();
        $attendanceId =(int)$request->query('id');
        $workDate = Carbon::parse($request->input('work_date'))->toDateString();
        $targetUserId = (int)($request->input('user_id') ?? $request->query('user_id'));
        if ($targetUserId <= 0) {
        return back()->withErrors(['user_id' => '対象ユーザーが不明です'])->withInput();
         }

        if($attendanceId >0){
            $attendance = Attendance::with('rests')->find($attendanceId);
            if (!$attendance) {
            return back()->withErrors(['id' => '勤怠が見つかりません'])->withInput();
            }
            }else{
                $attendance = Attendance::firstOrCreate(
                    ['user_id'=> $targetUserId, 'work_date'=>$workDate],['work_status'=>'退勤済'])
            ->load('rests');
            }


        $changeAttendance = ChangeAttendance::create([
            'attendance_id' => $attendance->id,
            'user_id' => $targetUserId,
            'work_date' => $workDate,
            'requested_clock_in_time' => $validated['requested_clock_in_time'],
            'requested_clock_out_time' => $validated['requested_clock_out_time'],
            'notes' => $validated['notes'] ?? null,
            'approval_status' => '承認待ち',
        ]);

        if (!empty($validated['requested_break_start_time']) || !empty($validated['requested_break_end_time'])){
            $rest = $attendance?->rests->first();
        ChangeRest::create([
            'change_attendance_id' => $changeAttendance->id,
            'rest_id' => $rest?->id,
            'requested_break_start_time' => $validated['requested_break_start_time'] ?? null,
            'requested_break_end_time' => $validated['requested_break_end_time'] ?? null,
            'approval_status' => '承認待ち',
        ]);
    }
        return redirect()->route('admin.stamp_correction_request.list')->with('message', '申請が完了しました');
    }

    public function showApprove(Request $request ,int $attendance_correct_request_id){

        $change = ChangeAttendance::with(['attendance', 'user', 'changeRests'])
        ->findOrFail($attendance_correct_request_id);

        if(empty($change->user_id)){
            $fallBackUserId = (int)$request->query('user_id');
            if($fallBackUserId > 0){
                $change->update(['user_id'=>$fallBackUserId]);
                $change->load('user');
            }
        }

        return view('admin.approve', compact('change'));
        }

    public function approve(Request $request, int $attendance_correct_request_id){

        $change = ChangeAttendance::with(['user', 'changeRests'])
        ->findOrFail($attendance_correct_request_id);
        
        if (empty($change->user_id)) {
        return back()->withErrors(['user_id' => '申請データに user_id が入っていません'])->withInput();
        }
        DB::transaction(function() use ($change) {
            if($change->attendance_id){
                $attendance = Attendance::find($change->attendance_id);
            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $change->user_id,
                    'work_date' => $change->work_date,
                ]);
                $change->update(['attendance_id' => $attendance->id]);
            }
            }else{
                $attendance = Attendance::create([
                    'user_id' => $change->user_id,
                    'work_date' => $change->work_date,
                ]); 
            $change->update(['attendance_id' => $attendance->id]);
            }

            $attendance->update([
                'work_date' => $change->work_date,
                'clock_in_time' => $change->requested_clock_in_time,
                'clock_out_time' => $change->requested_clock_out_time,
                'work_status' => '退勤済'
            ]);

            foreach ($change->changeRests as $changeRest) {
                if($changeRest->rest_id){
                    $rest = Rest::findOrFail($changeRest->rest_id);
                }else{
                    $rest = Rest::create([
                        'attendance_id' => $attendance->id,
                        'break_start_time' => $changeRest->requested_break_start_time,
                        'break_end_time' => $changeRest->requested_break_end_time,
                    ]);

                    $changeRest->update(['rest_id' => $rest->id]);
                    continue;
                }

                $rest->update([
                    'break_start_time' => $changeRest->requested_break_start_time,
                    'break_end_time' => $changeRest->requested_break_end_time,
                ]);
            }

            $change->update(['approval_status' => '承認済み']);
            foreach ($change->changeRests as $changeRest) {
                $changeRest->update(['approval_status' => '承認済み']);
            }
        });
        return redirect()->route('admin.stamp_correction_request.detail',[
            'attendance_correct_request_id'=>$change->id,
        ])->with('message', '申請を承認しました');
    }
}
