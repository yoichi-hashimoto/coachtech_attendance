<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $work_date = Carbon::today()->subDays(rand(0,10));

        $clockIn = $work_date->copy()
            ->setTime(
            rand(8,10),
            rand(0,59)
        );

        $clockOut = $clockIn->copy()
            ->addHours(rand(6,9));

        return [
            'work_date' => $work_date->toDateString(),
            'clock_in_time' => $clockIn->format('H:i'),
            'clock_out_time' => $clockOut->format('H:i'),
            'work_status' => '退勤済'
        ];
    }
}
