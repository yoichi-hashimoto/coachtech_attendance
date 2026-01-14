<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use App\Models\User;

class RestTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_rest_in_button()
    {
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00')->timezone('Asia/Tokyo'));
        $user= User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response ->assertOk()
            ->assertSee('休憩入');
    }

    public function test_rest_in_button_everytime(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00')->timezone('Asia/Tokyo'));
        $user= User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_start',
                'break_start_time' => '12:00:00',
                'work_status' => '休憩中'
            ]));
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_end',
                'break_end_time' => '13:00:00',
                'work_status' => '出勤中'
            ]));

        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response ->assertOk()
            ->assertSee('休憩入');
        }

    public function test_rest_return_button()
        {
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00')->timezone('Asia/Tokyo'));
        $user= User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_start',
                'break_in_time' => '12:00:00',
                'work_status' => '休憩中'
            ]));

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response ->assertOk()
            ->assertSee('休憩戻');
        }

    public function test_rest_return_button_everytime(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00:00')->timezone('Asia/Tokyo'));
        $user= User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_start',
                'break_start_time' => '12:00:00',
                'work_status' => '休憩中'
            ]));
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_end',
                'break_end_time' => '12:30:00',
                'work_status' => '出勤中'
            ]));

        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_start',
                'break_start_time' => '13:00:00',
                'work_status' => '休憩中'
            ]));

        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('attendance'));

        $response ->assertOk()
            ->assertSee('休憩戻');
        }

    public function test_attendance_breaktime_list_check(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date'=>'2026-01-01',
                'clock_in_time'=>'09:00',
            ]);
        $response -> assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_start',
                'break_start_time' => '12:00',
                'work_status' => '休憩中'
            ]));
        $response -> assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'break_end',
                'break_end_time' => '12:30',
                'work_status' => '出勤中'
            ]));
        $response -> assertStatus(302);

        $response= $this->actingAs($user)
            ->get(route('attendance.list'));
        $response->assertOk()
            ->assertSee('00:30');
    }

    public function test_attendance_work_off_button(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:00:00',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response ->assertOk()
            ->assertSee('退勤');

        $response = $this->actingAs($user)
            ->patch(route('attendance.update'),[
                'work_date' => '2026-01-01',
                'clock_out_time' => '17:00:00',
                'action' => 'clock_out',
                'work_status' => '退勤済',
            ]);
        $response ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('attendance'))
            ->assertSee('退勤済');
    }

    public function test_attendance_work_off_time_check(){
        $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role'=>'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'),[
                'work_date'=>'2026-01-01',
                'clock_in_time'=>'09:00',
            ]);
        $response -> assertStatus(302);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update',[
                'work_date' => '2026-01-01',
                'action' => 'clock_out',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]));
        $response -> assertStatus(302);

        $response= $this->actingAs($user)
            ->get(route('attendance.list'));
        $response->assertOk()
            ->assertSee('17:00');
    }
}
