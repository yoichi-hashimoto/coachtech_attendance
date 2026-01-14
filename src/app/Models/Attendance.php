<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','work_date','clock_in_time','clock_out_time',
        'break_start_time','break_end_time','notes','work_status','approval_status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function calcWorkMinutes(){
    
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return 0;
        }

        $clockIn  = Carbon::parse($this->clock_in_time);
        $clockOut = Carbon::parse($this->clock_out_time);

        $workingMinutes = $clockIn->diffInMinutes($clockOut);
        $workingMinutes -= $this->calcBreakMinutes();

        return max(0, $workingMinutes);
    }

    public function calcBreakMinutes(){
        return $this->rests
        ->filter(function($rest){
            return $rest ->break_start_time && $rest->break_end_time;
        })

        ->sum(function($rest){
            $breakStart = Carbon::parse($rest->break_start_time);
            $breakeEnd = $rest->break_end_time ? Carbon::parse($rest->break_end_time) : now();
            return max(0,$breakStart->diffInMinutes($breakeEnd));
        });
    }

    protected $casts = [
        'work_date' => 'date',
        'clock_in_time' => 'datetime:H:i',
        'clock_out_time' => 'datetime:H:i',
    ];

    public function rests(){
        return $this->hasMany(Rest::class);
    }

    public function changeAttendances(){
        return $this->hasMany(ChangeAttendance::class);
    }
}
