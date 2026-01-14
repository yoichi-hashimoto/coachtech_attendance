<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DownloadController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {return view('/login');});

Route::get('/admin/login',function () {return view('admin.login');})->name('admin.login');

Route::middleware(['auth','verified', 'role:user'])->group(function(){
    Route::get('/attendance', [UserController::class,'showAttendance'])->name('attendance');
    Route::post('/attendance', [AttendanceController::class,'store'])->name('attendance.store');
    Route::patch('/attendance', [AttendanceController::class,'update'])->name('attendance.update');
    Route::get('/attendance/list',[AttendanceController::class,'showList'])->name('attendance.list');
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'showDetail'])->name('attendance.detail');
    Route::get('/stamp_correction_request/list',[ApplicationController::class,'showList'])->name('stamp_correction_request');
    Route::post('/stamp_correction_request',[ApplicationController::class,'store'])->name('stamp_correction_request.store');
});

Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){
    Route::get('/attendance/list', [AttendanceController::class, 'showAllAttendance'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'showAllDetail'])->name('attendance.detail');
    Route::get('/staff/list',[UserController::class,'showStaffList'])->name('staff.list');
    Route::get('/stamp_correction_request/list', [ApplicationController::class,'showList'])->name('stamp_correction_request.list');
    Route::post('/stamp_correction_request/store', [ApplicationController::class,'storeForAdmin'])->name('stamp_correction_request.store');
    Route::get('/attendance/staff/{id}', [AttendanceController::class, 'showUsersAllAttendance'])->name('attendance.staff');
    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}',[ApplicationController::class ,'showApprove'])->name('stamp_correction_request.detail');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [ApplicationController::class,'approve'])->name('stamp_correction_request.approve');
    Route::get('/attendance/csv', [DownloadController::class, 'csvDownload'])->name('attendance.csv');
    });


