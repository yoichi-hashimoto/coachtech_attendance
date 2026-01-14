<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users =User::factory()->count(10)->create(['role'=>'user']);
        $admin =User::factory()->count(2)->create(['role'=>'admin']);

        $dates = collect(range(0,9))
            ->map(fn($i)=>Carbon::parse('2026-01-12')->subDays($i)->toDateString())->values();

        foreach($users as $user){
            foreach($dates as $date){
                if(random_int(1,100)<=20){continue;
            }
        

        $attendance = Attendance::factory()
            ->create([
                'user_id' => $user->id,
                'work_date'=> $date]);
        

        $restCount = random_int(0,2);
        Rest::factory()->count($restCount)->create([
            'attendance_id' => $attendance->id,
        ]);
    }
}}}
