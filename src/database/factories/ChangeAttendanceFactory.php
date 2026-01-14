<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ChangeAttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $work_date = Carbon::today()->subDays(rand(0,10));

        $reqClockIn = $work_date->copy()
            ->setTime(
            rand(8,10),
            rand(0,59)
        );

        $reqClockOut = $reqClockIn->copy()
            ->addHours(rand(6,9));

        return [
            'attendance_id' => null,
            'user_id' =>null,
            'work_date' => $work_date->toDateString(),
            'requested_clock_in_time' => $reqClockIn->format('H:i'),
            'requested_clock_out_time' => $reqClockOut->format('H:i'),
            'approval_status' => '承認待ち'
        ];

    }
}
