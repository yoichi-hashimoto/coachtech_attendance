<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChangeAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_date','user_id','attendance_id','requested_clock_in_time','requested_clock_out_time','approval_status','notes'
    ];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function changeRests(){
        return $this->hasMany(ChangeRest::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

        public function requestedInAt(): ?Carbon
    {
        if (!$this->work_date || !$this->requested_clock_in_time) return null;
        return Carbon::parse($this->work_date.' '.$this->requested_clock_in_time);
    }

    public function requestedOutAt(): ?Carbon
    {
        if (!$this->work_date || !$this->requested_clock_out_time) return null;
        return Carbon::parse($this->work_date.' '.$this->requested_clock_out_time);
    }

    public function grossWorkMinutes(): int
    {
        $in  = $this->requestedInAt();
        $out = $this->requestedOutAt();
        if (!$in || !$out) return 0;

        if ($out->lt($in)) {
            return 0;
        }

        return $in->diffInMinutes($out);
    }

    public function breakMinutes(): int
    {
        $rests = $this->relationLoaded('changeRests')
            ? $this->changeRests
            : $this->changeRests()->get();

        $total = 0;

        foreach ($rests as $rest) {
            $startStr = $rest->requested_break_start_time ?? null;
            $endStr   = $rest->requested_break_end_time ?? null;

            if (!$startStr || !$endStr) continue;

            $start = Carbon::parse($this->work_date.' '.$startStr);
            $end   = Carbon::parse($this->work_date.' '.$endStr);

            if ($end->lt($start)) {
                continue;
            }

            $total += $start->diffInMinutes($end);
        }

        return $total;
    }

    public function netWorkMinutes(): int
    {
        return max(0, $this->grossWorkMinutes() - $this->breakMinutes());
    }

}
