<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_attendance_work_start_button()
    {
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response ->assertSee('出勤');

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2024-06-15',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);
        $response = $this->get(route('attendance'));
        $response ->assertSee('出勤中');
    }

    public function test_attendance_work_start_button_once(){
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date'=>'2024-06-15',
                'clock_in_time'=>'09:00:00',
            ]);
        $response -> assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update'),[
                'work_date'=>'2024-06-15',
                'clock_out_time' =>'17:00:00',
                'action' =>'clock_out',
                'work_status' =>'退勤済',
            ]);
        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response->assertOk()
            ->assertDontSee('出勤');
    }

    public function test_attendance_work_start_list_check(){
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date'=>'2024-06-15',
                'clock_in_time'=>'09:00',
            ]);
        $response -> assertStatus(302);

        $response= $this->actingAs($user)
            ->get(route('attendance.list'));
        $response->assertOk()
            ->assertSee('09:00');
    }
}
