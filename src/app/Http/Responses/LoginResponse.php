<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (session()->has('url.intended')) {
            return redirect()->intended();
        }

        if ($user && $user->role === 'admin') {
            return redirect()->route('admin.attendance.list');
        }

        return redirect()->route('attendance');
    }
}
