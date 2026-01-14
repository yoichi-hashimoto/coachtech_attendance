<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function csvDownload(Request $request){
    $userId = (int)$request->query('user_id',$request->user()->id);
    $month = $request->query('month',now()->format('Y-m'));
    $month = str_replace('/', '-', $month);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $month)) {
        $month = substr($month, 0, 7);
    }
    $start = Carbon::createFromFormat('Y-m',$month)->startOfMonth();
    $end = (clone $start)->endOfMonth();

    $attendances = Attendance::where('user_id',$userId)
        ->whereDate('work_date', '>=', $start->toDateString())
        ->whereDate('work_date', '<=', $end->toDateString())
        ->with('rests','user')
        ->orderBy('work_date')
        ->get();
        
    $headers = [
        '名前','日付','出勤','退勤','休憩','合計'
    ];

    return new StreamedResponse(function()use($attendances,$headers){
        $handle = fopen('php://output','w');
        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle,$headers);

        foreach ($attendances as $a){
            $workDate = Carbon::parse($a->work_date)->format('Y年m月d日');
            $clockIn = $a->clock_in_time ? Carbon::parse($a->clock_in_time)->format('H:i') : '';
            $clockOut = $a->clock_out_time ? Carbon::parse($a->clock_out_time)->format('H:i') : '';
            $breakMin = $a->calcBreakMinutes();
            $workMin = $a->calcWorkMinutes();
            fputcsv($handle,[
                optional($a->user)->name ?? '',
                $workDate,
                $clockIn,
                $clockOut,
                $this->minutesToHhmm($breakMin),
                $this->minutesToHhmm($workMin),
                ]);
        }

        fclose($handle);
        },200,[
            'Content-Type' =>'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance.csv"',
        ]);
    }

    private function minutesToHhmm(int $minutes): string
    {
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    return sprintf('%02d:%02d', $h, $m);
    }

}
