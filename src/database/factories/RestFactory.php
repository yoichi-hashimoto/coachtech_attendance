<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class RestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $workDate = Carbon::today()->subDays(rand(0,10));

        $breakStart = $workDate->copy()
            ->setTime(12,0)
            ->addMinutes(rand(0,180));

        $breakEnd = $breakStart->copy()
            ->addMinutes(rand(30,60));
        return [
            'break_start_time' => $breakStart->format('H:i'),
            'break_end_time' => $breakEnd->format('H:i'),
        ];
    }
}
