<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;

class DateTimeTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_date_time_now()
    {
        config(['app.timezone' => 'Asia/Tokyo']);
        date_default_timezone_set('Asia/Tokyo');

        $fixed = Carbon::create(2024, 6, 15, 14, 30, 0);
        Carbon::setTestNow('2024-06-15 14:30:00');

        $response = $this->actingAs(User::factory()->create(['role' => 'user']))
            ->get('/attendance');

        $response->assertOk();

        $response->assertViewHas('today', function ($v) use ($fixed) {
            return $v instanceof Carbon
                && $v->isoFormat('Y年MM月DD日ddd曜日') === $fixed->isoFormat('Y年MM月DD日ddd曜日');
        });
        $response->assertViewHas('now', function ($v) {
        return $v instanceof Carbon
            && $v->format('H:i') === now()->format('H:i');
        });

        Carbon::setTestNow();
    }

    public function test_attendance_work_status_not_started()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response->assertOk();
        $response->assertSee('勤務外');
    }

    public function test_attendance_work_status_started()
    {
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)
            ->post(route('attendance.store'), [
                'work_date' => '2024-06-15',
                'clock_in_time' => '09:00:00',
            ]);
        $response->assertRedirect(route('attendance'));

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'work_date' => '2024-06-15',
            'clock_in_time' => '09:00:00',
            'work_status' => '出勤中',
        ]);


        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response->assertOk();
        $response->assertViewHas('status', '出勤中');
    }

    public function test_attendance_work_status_rest(){
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('attendance.store'), [
                'work_date' => '2024-06-15',
                'clock_in_time' => '09:00:00',
            ]);

        $response = $this->actingAs($user)
            ->patch(route('attendance.update'), [
                'work_date' => '2024-06-15',
                'action' => 'break_start',
                'break_start_time' => '12:00:00',
            ]);
        $response->assertStatus(302);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'work_date' => '2024-06-15',
            'work_status' => '休憩中',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance'));
        $response->assertOk();
        $response->assertSee('休憩中');

    }

    public function test_attendance_work_status_off(){
        $this->travelTo(Carbon::parse('2024-06-15 09:00:00')->timezone('Asia/Tokyo'));

        $user = User::factory()->create(['role'=>'user']);
        
        $response = $this->actingAs($user)
            ->post(route('attendance.store'), [
                'work_date' => '2024-06-15',
                'clock_in_time' => '09:00:00',
            ]);
        $response->assertStatus(302);

            $response = $this->actingAs($user)
            ->patch(route('attendance.update'), [
                'work_date' => '2024-06-15',
                'clock_out_time' => '17:00:00',
                'action' => 'clock_out',
                'work_status' => '退勤済'
            ]);

        $response = $this->actingAs($user)
                 ->get(route('attendance'));
        $response->assertOk()
                 ->assertSee('退勤済');
    }
}
