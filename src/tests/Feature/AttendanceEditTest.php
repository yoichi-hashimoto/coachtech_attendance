<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use App\Models\Changeattendance;

class AttendanceEditTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_attendance_requet_intime_validation()
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


        $response = $this->actingAs($user)
            ->post(route('stamp_correction_request.store',[
                'requested_clock_in_time' => '18:00',
                'requested_clock_out_time' => '09:00',
                'mptes' => 'test',
            ]));

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors([
            'requested_clock_in_time' =>'出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    public function test_attendance_rest_intime_validation()
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


        $response = $this->actingAs($user)
            ->post(route('stamp_correction_request.store',[
                'requested_clock_in_time' => '09:00',
                'requested_clock_out_time' => '17:00',
                'requested_break_start_time' => '18:00',
                'requested_break_end_time' => '13:00',
                'notes' => 'test',
            ]));

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors([
            'requested_break_start_time' =>'休憩時間が不適切な値です',
        ]);
    }

    public function test_attendance_notes_validation()
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


        $response = $this->actingAs($user)
            ->post(route('stamp_correction_request.store',[
                'requested_clock_in_time' => '09:00',
                'requested_clock_out_time' => '17:00',
                'requested_break_start_time' => '12:00',
                'requested_break_end_time' => '13:00',
                'notes' => null ,
            ]));

        $response -> assertStatus(302);
        $response -> assertSessionHasErrors([
            'notes' =>'備考を記入してください',
        ]);
    }

    public function test_attendance_application_edit()
        {
        $user = User::factory()->create(['name'=>'guest','role'=>'user']);
        $admin = User::factory()->create(['role'=>'admin']);
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
            ->post(route('stamp_correction_request.store'),[
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2026-01-01',
                'requested_clock_in_time' => '09:30',
                'requested_clock_out_time' => '17:30',
                'requested_break_start_time' => '12:00',
                'requested_break_end_time' => '13:00',
                'notes' => 'test',
            ])
            ->assertStatus(302);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail',[$attendance->id]));
        $response -> assertOk()
            -> assertSeeInOrder([
            '09:30','17:30','12:00','13:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.stamp_correction_request.list'));

        $response -> assertOk()
            -> assertSeeInOrder([
            'guest','2026/01/01','test',
        ]);
    }

    public function test_attendance_application_check()
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
            ->post(route('stamp_correction_request.store'),[
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2026-01-01',
                'requested_clock_in_time' => '09:30',
                'requested_clock_out_time' => '17:30',
                'requested_break_start_time' => '12:00',
                'requested_break_end_time' => '13:00',
                'notes' => 'test',
            ])
            ->assertStatus(302);

        $response = $this->actingAs($user)
            ->get(route('stamp_correction_request'));
        $response -> assertOk()
            -> assertSeeInOrder([
            '承認待ち','guest','2026/01/01','test',
        ]);
        }

   public function test_attendance_approve_check()
        {
        $user = User::factory()->create(['name'=>'guest','role'=>'user']);
        $admin = User::factory()->create(['role'=>'admin']);
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
            ->post(route('stamp_correction_request.store'),[
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2026-01-01',
                'requested_clock_in_time' => '09:30',
                'requested_clock_out_time' => '17:30',
                'requested_break_start_time' => '12:00',
                'requested_break_end_time' => '13:00',
                'notes' => 'test',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('change_attendances', [
        'user_id' => $user->id,
        'work_date' => '2026-01-01',
        'approval_status' => '承認待ち',
    ]);

        $change = ChangeAttendance::where('user_id',$user->id)
            ->whereDate('work_date','2026-01-01')
            ->latest()->first();

        $this->assertNotNull($change);

        $this ->actingAs($admin)
            ->post(route('admin.stamp_correction_request.approve',['attendance_correct_request_id'=>$change->id]))
            ->assertStatus(302);
                
        $this->assertDatabaseHas('change_attendances',['id'=>$change->id,'approval_status'=>'承認済み',]);

        $this->actingAs($user)
            ->get(route('stamp_correction_request',['status'=>'approved']))
            -> assertOk()
            -> assertSeeInOrder([
            '承認済み','guest','2026/01/01','test',
        ]);
    }

    public function test_attendance_application_detail(){
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
            ->post(route('stamp_correction_request.store'),[
                'attendance_id' => $attendance->id,
                'user_id' => $user->id,
                'work_date' => '2026-01-01',
                'requested_clock_in_time' => '09:30',
                'requested_clock_out_time' => '17:30',
                'requested_break_start_time' => '12:00',
                'requested_break_end_time' => '13:00',
                'notes' => 'test',
            ])
            ->assertStatus(302);

        $this->actingAs($user)
            ->get(route('attendance.list'))
            ->assertStatus(200);
        
        $this->actingAs($user)
            ->get(route('attendance.detail',[$attendance->id]))
            ->assertStatus(200);
    }
}