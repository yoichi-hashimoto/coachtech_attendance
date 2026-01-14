<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_attendance_list_view(){
        $user = User::factory()->create(['role'=>'user']);
        $this->actingAs($user);
        
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
            ->get(route('attendance.list'));

        $response -> assertStatus(200);

        $response -> assertSeeInOrder([
            '01/01/(木)','09:00','17:00','01:00'
        ]);
    }

    public function test_this_month_view(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response -> assertStatus(200);
        $response -> assertSee(2026/01);
    }

    public function test_previous_month_view(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create([
            'role' => 'user',
        ]);

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

        $prevMonth = Carbon::now('Asia/Tokyo') -> subMonth()->format('Y/m');

        $response = $this->actingAs($user)
            ->get(route('attendance.list',['month'=>$prevMonth]));

        $response -> assertStatus(200);
        $response -> assertSee('2025/12');
    }

        public function test_next_month_view(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create([
            'role' => 'user',
        ]);

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

        $nextMonth = Carbon::now('Asia/Tokyo') -> addMonth()->format('Y/m');

        $response = $this->actingAs($user)
            ->get(route('attendance.list',['month'=>$nextMonth]));

        $response -> assertStatus(200);
        $response -> assertSee('2026/02');
    }

    public function test_attendance_detail_view(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create([
            'role' => 'user',
        ]);

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

        $nextMonth = Carbon::now('Asia/Tokyo') -> addMonth()->format('Y/m');

        $response = $this->actingAs($user)
            ->get(route('attendance.list'));

        $response = $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]));

        $response -> assertStatus(200);
        }
}
