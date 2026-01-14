<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ChangeRestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $workDate = Carbon::today()->subDays(rand(0,10));

        $reqBreakStart = $workDate->copy()
            ->setTime(12,0)
            ->addMinutes(rand(0,180));

        $reqBreakEnd = $reqBreakStart->copy()
            ->addMinutes(rand(30,60));
        return [
            'change_attendance_id' => null,
            'requested_break_start_time' => $reqBreakStart->format('H:i'),
            'requested_break_end_time' => $reqBreakEnd->format('H:i'),
        ];
    }
}
