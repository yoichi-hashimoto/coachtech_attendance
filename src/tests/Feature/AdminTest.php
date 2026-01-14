<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
Use App\Models\ChangeAttendance;
use App\Models\ChangeRest;
use App\Models\Rest;
use Carbon\Carbon;

class AdminTest extends TestCase
{

    use RefreshDatabase;

    public function test_all_user_attendance_check()
    {
        $user = User::factory()->create(['role'=>'user']);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-01-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $admin = User::factory()->create(['role'=>'admin']);

        $this->actingAs($admin)
            ->get(route('admin.attendance.list',['day'=>'2026-01-01']))
            ->assertStatus(200)
            ->assertViewHas('attendances');
    }

    public function test_work_date_check()
        {
        $user = User::factory()->create(['role'=>'user']);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2026-01-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $admin = User::factory()->create(['role'=>'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.list',['day'=>'2026-01-01']))
            ->assertStatus(200)
            ->assertSee('2026/01/01');
}
    public function test_admin_previous_day(){
            $today = Carbon::create('2026-01-01')->timezone('Asia/Tokyo');
            $previousDay = $today ->copy()->subDay();
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)
            ->get(route('admin.attendance.list',['day'=>$previousDay->format('Y-m-d')]))
            ->assertSee('2025/12/31');
    }

    public function test_admin_next_day(){
            $today = Carbon::create('2026-01-01')->timezone('Asia/Tokyo');
            $nextDay = $today ->copy()->addDay();
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)
            ->get(route('admin.attendance.list',['day'=>$nextDay->format('Y-m-d')]))
            ->assertSee('2026/01/02');
    }

    public function test_admin_attendance_detail(){
        $admin = User::factory()->create(['role'=>'admin']);
        $user = User::factory()->create(['role'=>'user']);
        $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);
            $admin = User::factory()->create(['role'=>'admin']);

            $response = $this->actingAs($admin)->get(route('admin.attendance.detail',[$attendance->id]))
                ->assertStatus(200);
            
            $response -> assertSee('2026年')
                ->assertSee('01月01日')
                ->assertSee($user->name)
                ->assertSee('09:00')
                ->assertSee('17:00');
    }

    public function test_admin_clock_in_time_validation()
    {
            $user = User::factory()->create(['role'=>'user']);
            $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)->get(route('admin.attendance.detail',[$attendance->id]))
                ->assertStatus(200);

            $response = $this->actingAs($admin)
                ->post(route('admin.stamp_correction_request.store',[$attendance->id]),[
                    'requested_clock_in_time' => '18:00',
                    'requested_clock_out_time' => '17:00',
                    'notes' => 'test'
                ]);

            $response ->assertRedirect();
            $response ->assertSessionHasErrors();

            $errors = session('errors')->all();
            $this->assertContains('出勤時間もしくは退勤時間が不適切な値です', $errors);
    }

    public function test_admin_break_start_time_validation()
    {
            $user = User::factory()->create(['role'=>'user']);
            $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)->get(route('admin.attendance.detail',[$attendance->id]))
                ->assertStatus(200);

            $response = $this->actingAs($admin)
                ->post(route('admin.stamp_correction_request.store',[$attendance->id]),[
                    'requested_clock_in_time' => '09:00',
                    'requested_clock_out_time' => '17:00',
                    'requested_break_start_time' => '18:00',
                    'requested_break_end_time' => '18:30',
                    'notes' => 'test'
                ]);

            $response ->assertRedirect();
            $response ->assertSessionHasErrors();

            $errors = session('errors')->all();
            $this->assertContains('休憩時間が不適切な値です', $errors);
    }

    public function test_admin_break_end_time_validation()
    {
            $user = User::factory()->create(['role'=>'user']);
            $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)->get(route('admin.attendance.detail',[$attendance->id]))
                ->assertStatus(200);

            $response = $this->actingAs($admin)
                ->post(route('admin.stamp_correction_request.store',[$attendance->id]),[
                    'requested_clock_in_time' => '09:00',
                    'requested_clock_out_time' => '17:00',
                    'requested_break_start_time' => '12:00',
                    'requested_break_end_time' => '18:00',
                    'notes' => 'test'
                ]);

            $response ->assertRedirect();
            $response ->assertSessionHasErrors();

            $errors = session('errors')->all();
            $this->assertContains('休憩時間もしくは退勤時間が不適切な値です', $errors);
    }

    public function test_admin_notes_validation()
    {
            $user = User::factory()->create(['role'=>'user']);
            $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);
            $admin = User::factory()->create(['role'=>'admin']);

            $this->actingAs($admin)->get(route('admin.attendance.detail',[$attendance->id]))
                ->assertStatus(200);

            $response = $this->actingAs($admin)
                ->post(route('admin.stamp_correction_request.store',[$attendance->id]),[
                    'requested_clock_in_time' => '09:00',
                    'requested_clock_out_time' => '17:00',
                    'requested_break_start_time' => '12:00',
                    'requested_break_end_time' => '13:00',
                    'notes' => null
                ]);

            $response ->assertRedirect();
            $response ->assertSessionHasErrors();

            $errors = session('errors')->all();
            $this->assertContains('備考を記入してください', $errors);
    }

    public function test_admin_allUser_list(){
            $users = User::factory()->count(5)->create(['role' =>'user']);
            $admin = USer::factory()->create(['role'=>'admin']);

            $response = $this->actingAs($admin)
                ->get(route('admin.staff.list'))
                ->assertOk()
                ->assertViewHas('users');
    }

    public function test_admin_usersAttendance(){
            $admin = USer::factory()->create(['role'=>'admin']);
            $user = User::factory()->create(['name'=>'John','role'=>'user']);
            $attendance = Attendance::create([
                'work_date' => '2026-01-01',
                'user_id' => $user->id,
                'clock_in_time' => '09:00',
                'clock_out_time' => '17:00',
                'work_status' => '退勤済'
            ]);

            $response = $this->actingAs($admin)
                ->get(route('admin.attendance.staff',[$user->id]))
                ->assertStatus(200);

            $response -> assertOk()
                ->assertSee('John')
                ->assertViewHas('days');
    }

    public function test_admin_user_list_previous_month(){
            $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
            $user = User::factory()->create(['role' =>'user']);
            $admin = USer::factory()->create(['role'=>'admin']);

            $prevMonth = Carbon::now()->subMonth()->format('Y/m');

            $response = $this->actingAs($admin)
                ->get(route('admin.attendance.staff',['id'=>$user->id]).'?month=' . $prevMonth)
                ->assertOk()
                ->assertSee('2025/12');
    }

    public function test_admin_user_list_next_month(){
            $this->travelTo(Carbon::parse('2026-01-01 09:00')->timezone('Asia/Tokyo'));
            $user = User::factory()->create(['role' =>'user']);
            $admin = USer::factory()->create(['role'=>'admin']);

            $nextMonth = Carbon::now()->addMonth()->format('Y/m');

            $response = $this->actingAs($admin)
                ->get(route('admin.attendance.staff',['id'=>$user->id,'month'=>$nextMonth]))
                ->assertOk()
                ->assertSee('2026/02');
    }

    public function test_admin_user_attendance_detail(){
            $user = User::factory()->create(['role' =>'user']);
            $admin = USer::factory()->create(['role'=>'admin']);

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

        $this->actingAs($admin)
            ->get(route('admin.attendance.staff',['id'=>$user->id]))
            ->assertStatus(200);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail',[$attendance->id]))
            ->assertStatus(200);
    }

    public function test_admin_stamp_correction_request_view(){
        $admin = User::factory()->create(['role'=>'admin']);
        $userA = User::factory()->create(['role'=>'user','name'=>'1111']);
        $userB = User::factory()->create(['role'=>'user','name'=>'2222']);

        $attendanceA = Attendance::factory()->create([
            'user_id' => $userA->id,
            'work_date' => '2026-01-04'
        ]);

        $attendanceB = Attendance::factory()->create([
            'user_id' => $userA->id,
            'work_date' => '2026-01-02'
        ]);

        $pending1 = ChangeAttendance::factory()->create([
            'user_id' => $userA->id,
            'attendance_id' => $attendanceA->id,
            'approval_status' =>'承認待ち',
            'work_date' => '2026-01-04'
        ]);

        $pending2 = ChangeAttendance::factory()->create([
            'user_id' => $userB->id,
            'attendance_id' => $attendanceB->id,
            'approval_status' =>'承認待ち',
            'work_date' => '2026-01-04'
        ]);

        ChangeAttendance::factory()->create([
            'user_id' =>$userA->id,
            'attendance_id' => $attendanceA->id,
            'approval_status' => '承認済み',
            'work_date'=>'2026-01-01',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.stamp_correction_request.list',['status'=>'pending']))
            ->assertOk()
            ->assertViewHas('status','pending');


            $all = $response->viewData('allApplications');

        $response->assertViewHas('allApplications',function($allApplications) use ($pending1,$pending2){
            $ids = $allApplications->pluck('id')->all();

            return in_array($pending1->id,$ids,true)
                && in_array($pending2->id,$ids,true)
                && $allApplications->every(fn($a)=> $a->approval_status === '承認待ち');
        });

        $response ->assertSee('1111');
        $response ->assertSee('2222');

    }

    public function test_admin_stamp_correction_request_approved_view(){
        $admin = User::factory()->create(['role'=>'admin']);
        $userA = User::factory()->create(['role'=>'user','name'=>'1111']);
        $userB = User::factory()->create(['role'=>'user','name'=>'2222']);

        $attendanceA = Attendance::factory()->create([
            'user_id' => $userA->id,
            'work_date' => '2026-01-04'
        ]);

        $attendanceB = Attendance::factory()->create([
            'user_id' => $userA->id,
            'work_date' => '2026-01-02'
        ]);

        $approved1 = ChangeAttendance::factory()->create([
            'user_id' => $userA->id,
            'attendance_id' => $attendanceA->id,
            'approval_status' =>'承認済み',
            'work_date' => '2026-01-04'
        ]);

        $approved2 = ChangeAttendance::factory()->create([
            'user_id' => $userB->id,
            'attendance_id' => $attendanceB->id,
            'approval_status' =>'承認済み',
            'work_date' => '2026-01-04'
        ]);

        ChangeAttendance::factory()->create([
            'user_id' =>$userA->id,
            'attendance_id' => $attendanceA->id,
            'approval_status' => '承認待ち',
            'work_date'=>'2026-01-01',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.stamp_correction_request.list',['status'=>'approved']))
            ->assertOk()
            ->assertViewHas('status','approved');

            $all = $response->viewData('allApplications');

        $response->assertViewHas('allApplications',function($allApplications) use ($approved1,$approved2){
            $ids = $allApplications->pluck('id')->all();

            return in_array($approved1->id,$ids,true)
                && in_array($approved2->id,$ids,true)
                && $allApplications->every(fn($a)=> $a->approval_status === '承認済み');
        });

        $response ->assertSee('1111');
        $response ->assertSee('2222');
    }

    public function test_admin_stamp_correction_request_detail(){
        $user = User::factory()->create(['role' =>'user','name' =>'jack']);
        $admin = User::factory()->create(['role'=>'admin']);

        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.attendance.detail',['id'=>$attendance->id]));
        $response -> assertOk()
            ->assertSeeInOrder([
                'jack','2026年','01月01日','09:00','17:00','12:00','13:00'
            ]);
    }

    public function test_admin_stamp_correction_request_approval(){
        $user = User::factory()->create(['role' =>'user','name' =>'jack']);
        $admin = User::factory()->create(['role'=>'admin']);

        $attendance=Attendance::create([
            'user_id' => ($user->id),
            'work_date' => '2026-01-01',
            'clock_in_time' => '09:00',
            'clock_out_time' => '17:00',
            'work_status' => '退勤済',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'work_date' =>'2026-01-01',
            'break_start_time' => '12:00',
            'break_end_time' => '13:00',
        ]);

        $changeAttendance = ChangeAttendance::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'work_date' => '2026-01-01',
            'requested_clock_in_time' => '09:30',
            'requested_clock_out_time' =>'17:30',
            'approval_status' => '承認待ち'
        ]);

        $changeRest = ChangeRest::create([
            'change_attendance_id' => $changeAttendance->id,
            'requested_break_start_time' => '12:30',
            'requested_break_end_time' =>'13:30',
            'approval_status' =>'承認待ち'
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.stamp_correction_request.approve',['attendance_correct_request_id'=>$changeAttendance->id,]))
            ->assertStatus(302);

        $this ->assertDatabaseHas(
                table:'attendances',
                data:[
                'user_id' => $user->id,
                'work_date' => '2026-01-01',
                'clock_in_time' => '09:30:00',
                'clock_out_time' => '17:30:00',
            ]);

        $this ->assertDatabaseHas(
                table:'rests',
                data:[
                'attendance_id' => $attendance->id,
                'break_start_time' => '12:30:00',
                'break_end_time' => '13:30:00',
                ]);
    }
}