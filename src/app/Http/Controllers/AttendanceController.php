<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Rest;
use App\Models\ChangeAttendance;
use App\Models\User;

class AttendanceController extends Controller
{

    public function store(Request $request){
        $user = auth()->user();
        $today = $request->input('work_date');
        $now = $request->input('clock_in_time');
        $intime = Attendance::updateOrCreate([
            'user_id' => $user->id,
            'work_date' => $today,
            'clock_in_time' => $now,
            'work_status' => '出勤中',
        ]);
        return redirect()->route('attendance');
    }

    public function update(Request $request){
        $user = auth() ->user();
        $today = $request->input('work_date');
        $action = $request->input('action');
        $attendance = Attendance::where('user_id',$user->id)
            ->whereDate('work_date',$today)
            ->latest()
            ->firstOrFail();

        switch($action){
            case('clock_out'):
                $attendance->update([
                    'clock_out_time' => $request->input('clock_out_time'),
                    'work_status' => '退勤済'
                ]);
                break;
            case('break_start'):
                $attendance->rests()->create([
                    'break_start_time' => $request->input('break_start_time'),
                ]);
                $attendance->update([
                    'work_status' => '休憩中'
                ]);
                break;
            case('break_end'):
                $attendance->rests()->update([
                    'break_end_time' => $request->input('break_end_time'),
                ]);
                $attendance->update([
                    'work_status' => '出勤中'
                ]);
                break;
        }
        return redirect()->route('attendance',compact('user'));
    }

    public function showList(Request $request){
        $user = auth()->user();

        $target = $request->input('month')
            ?Carbon::createFromFormat('Y/m',$request->input('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $startDate= $target->copy()->startOfMonth();
        $endDate= $target->copy()->endOfMonth();

        $attendancesByDate = Attendance::where('user_id',$user->id)
            ->whereBetween('work_date',[$startDate->toDateString(),$endDate->toDateString()])
            ->with('rests')
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->work_date)->toDateString();
            });

        $changes = ChangeAttendance::where('user_id',$user->id)
            ->wherebetween('work_date',[$startDate->toDateString(),$endDate->toDateString()])
            ->with('changeRests')
            ->orderBy('created_at','desc')
            ->get()
            ->groupBy(fn($c)=>Carbon::parse($c->work_date)->toDateString())
            ->map(fn($g)=>$g->first());

        $days = collect(CarbonPeriod::create($startDate,$endDate))->map(function ($date) use ($attendancesByDate,$changes) {
            $key = $date->toDateString();
            $attendance = $attendancesByDate->get($key);
            $change = $changes->get($key);
            return [
                'date' => $date->copy(),
                'attendance' => $attendance,
                'latestChange' => $change,
                'totalBreakMinutes' => $attendance ? $attendance->calcBreakMinutes() : 0,
                'workMinutes' => $attendance ? $attendance->calcWorkMinutes() : 0,
            ];
        });

        $thismonth = $target->format('Y/m');
        $prevMonth = $target->copy()->subMonth()->format('Y/m');
        $nextMonth = $target->copy()->addMonth()->format('Y/m');

        return view('list',compact('user','days','thismonth','prevMonth','nextMonth'));
    }

    public function showDetail(Request $request, $id){
        $user = auth()->user();
        if((int)$id !==0){
            $attendance = Attendance::where('id', $id)
                ->where('user_id', $user->id)
                ->with('rests')
                ->firstOrFail();
        
        $workDate = Carbon::parse($attendance->work_date)->toDateString();
        }
        else{
            $workDate = $request->query('work_date');
            if(!$workDate){
                abort(404);
            }
            $workDate = Carbon::parse($workDate)->toDateString();
            $attendance = null;
        }

        $latestChange = ChangeAttendance::where('user_id', $user->id)
            ->whereDate('work_date', $workDate)
            ->orderByDesc('created_at')
            ->with('changeRests')
            ->first();

        $rests = $attendance?->rests ?? collect();
        $restChanges = $latestChange?->changeRests ?? collect();

            return view('detail',[
                'user' => $user,
                'attendance' => $attendance,
                'rests' => $rests,
                'latestChange' => $latestChange,
                'restChanges' => $restChanges,
                'workDate' => Carbon::parse($workDate),
            ]);
    }

    public function showAllAttendance(Request $request){
        $target = $request->input('day')
            ? Carbon::createFromFormat('Y-m-d', $request->input('day'))->startOfDay()
            : now()->startOfDay();
        $today = $target->toDateString();
        $previousDay = $target->copy()->subDay();
        $nextDay = $target->copy()->addDay();
        $attendances = Attendance::whereDate('work_date',$today)
            ->with('rests','user')
            ->get();
        $changesByAttendanceId = ChangeAttendance::whereDate('work_date',$today)
            ->whereNotNull('attendance_id')
            ->get()
            ->keyBy('attendance_id');
        
        foreach ($attendances as $a){
            $a->setRelation('changeAttendance',$changesByAttendanceId->get($a->id));
        }

        return view('admin.list', compact('attendances','previousDay','nextDay','target','today'));
    }

    public function showUsersAllAttendance(Request $request, $id){
        $user = User::findOrFail($id);

        $target = $request->input('month')
            ?Carbon::createFromFormat('Y/m',$request->input('month'))->startOfMonth()
            : Carbon::now()->startOfMonth();

        $startDate= $target->copy()->startOfMonth();
        $endDate= $target->copy()->endOfMonth();

        $attendancesByDate = Attendance::where('user_id',$user->id)
            ->whereBetween('work_date',[$startDate->toDateString(),$endDate->toDateString()])
            ->with('rests')
            ->get()
            ->keyBy(function ($attendance) {
                return Carbon::parse($attendance->work_date)->toDateString();
            });

        $changes = ChangeAttendance::where('user_id',$user->id)
            ->wherebetween('work_date',[$startDate->toDateString(),$endDate->toDateString()])
            ->with('changeRests')
            ->orderBy('created_at','desc')
            ->get()
            ->groupBy(fn($c)=>Carbon::parse($c->work_date)->toDateString())
            ->map(fn($g)=>$g->first());

        $days = collect(CarbonPeriod::create($startDate,$endDate))->map(function ($date) use ($attendancesByDate,$changes) {
            $key = $date->toDateString();
            $attendance = $attendancesByDate->get($key);
            $change = $changes->get($key);
            return [
                'date' => $date->copy(),
                'attendance' => $attendance,
                'latestChange' => $change,
                'totalBreakMinutes' => $attendance ? $attendance->calcBreakMinutes() : 0,
                'workMinutes' => $attendance ? $attendance->calcWorkMinutes() : 0,
            ];
        });

        $thismonth = $target->format('Y/m');
        $prevMonth = $target->copy()->subMonth()->format('Y/m');
        $nextMonth = $target->copy()->addMonth()->format('Y/m');

        return view('admin.attendance',compact('user','days','thismonth','prevMonth','nextMonth'));
    }

    public function showAllDetail(Request $request, $id){
        $id = (int)$id;
        if ($id === 0) {
        abort_unless($request->filled('user_id') && $request->filled('work_date'), 404);

        $userId = (int)$request->input('user_id');
        $workDate = Carbon::parse($request->input('work_date'))->startOfDay();

        $attendance = new Attendance();
        $attendance->id = 0;
        $attendance->user_id = (int)$userId;
        $attendance->work_date = $workDate->toDateString();
        $attendance->clock_in_time = null;
        $attendance->clock_out_time = null;
        $attendance->setRelation('user', \App\Models\User::findOrFail($userId));

        $rests = collect();
        } else {
        $attendance = Attendance::with('rests', 'user')->findOrFail($id);

        $userId = (int) $attendance->user_id;
        $workDate = Carbon::parse($attendance->work_date)->startOfDay();
        $rests = $attendance->rests;
        }
        $latestChange = ChangeAttendance::where('user_id', $userId)
            ->whereDate('work_date',$workDate->toDateString())
            ->orderByDesc('created_at')
            ->with('changeRests')
            ->first();

        return view('admin.detail',[
            'attendance' => $attendance,
            'rests' => $rests,
            'workDate' => $workDate,
            'latestChange' => $latestChange,
            'restChanges' => $latestChange?->changeRests ?? collect(),
            'isNewAttendance' => ((int)$id === 0),
        ]);
    }
}