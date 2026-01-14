<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{

    use RefreshDatabase;

    public function test_attendance_detail_user_name()
        {
        $user = User::factory()->create(['name'=>'guest','role'=>'user']);
        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '9:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => ($attendance->id),
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]));
        $response -> assertOk()
            ->assertSee('guest');
        }

    public function test_attendance_detail_date()
        {
        $user = User::factory()->create(['role'=>'user']);
        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '9:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => ($attendance->id),
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]));
        $response -> assertOk()
            ->assertSeeInOrder(['2026年','01月01日']);
        }

    public function test_attendance_work_time_correct(){
        $user = User::factory()->create(['role'=>'user']);
        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '9:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => ($attendance->id),
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]));
        $response -> assertOk()
            ->assertSeeInOrder(['9:00','17:00']);
         
    }
    public function test_attendance_rest_time_correct(){
        $user = User::factory()->create(['role'=>'user']);
        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '9:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => ($attendance->id),
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]));
        $response -> assertOk()
            ->assertSeeInOrder(['12:00','13:00']);
         
    }

}
