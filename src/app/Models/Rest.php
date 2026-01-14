<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id','break_start_time','break_end_time',
    ];

    public function attendance(){
        return $this->belongsTo(Attendance::class);
    }

    public function changeRestTime(){
        return $this->hasMany(ChangeRest::class);
    }
}
