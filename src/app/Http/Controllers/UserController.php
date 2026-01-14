<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class UserController extends Controller
{
    public function showAttendance(){
        $user = auth()->user();
        $today = Carbon::now()->timezone('Asia/Tokyo'); 
        $now = Carbon::now()->timezone('Asia/Tokyo');

        $intime = Attendance::where('user_id',$user->id)
            ->wheredate('work_date',$today)
            ->latest()
            ->first();
        $status = optional($intime)->work_status;
        return view('attendance',compact('today','now','user','intime','status'))->with('message','登録しました');
    }

    public function showStaffList(){
        $users = User::distinct()
            ->get();
        return view('admin.staff',compact('users'));
    }

    public function showAdminLoginForm(){
        return view('admin.login');
    } 

    public function AdminLogin(Request $request){
        $credentials = $request->only('email','password');

        if (auth()->guard('admins')->attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended('/admin/attendance/list');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
