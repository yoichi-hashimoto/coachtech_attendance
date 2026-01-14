<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChangeRest extends Model
{
    use HasFactory;

    protected $fillable = [
        'change_attendance_id','rest_id','requested_break_start_time','requested_break_end_time','approval_status',
    ];

    public function rest(){
        return $this->belongsTo(Rest::class);
    }

}
