<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Requests\LoginRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    $user = $request->user();
                    return redirect()->intended(
                        $user->role === 'admin' ? '/admin/attendance/list' : '/attendance');
                }
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::verifyEmailView(function(){
            return view('authmail');
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function(){
            return view('register');
        });

        Fortify::loginView(function(Request $request){
            if(request()->is('admin/login')){
                return view('admin.login');
            }
            return view('login');
        });

        Fortify::authenticateUsing(function (Request $request){
            $form = app(LoginRequest::class);
            $validator = Validator::make(
                $request->all(),
                $form->rules(),
                $form->messages()
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validated = $validator->validated();

            $user = User::where('email', $validated['email'])->first();

            if(! $user){
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません'
                ]);
            }

            if ($request->input('login_type') === 'admin' && $user->role !== 'admin'){
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません'
                ]);
            }

            if(!Auth::attempt(
                ['email' => $validated['email'], 'password' => $validated['password']],
            )){
                throw ValidationException::withMessages([
                    'password' => 'パスワードが正しくありません'
                ]);
            }
                return Auth::user();
        });

        RateLimiter::for('login',function(Request $request){
            $email = (string) $request -> email;

            return Limit::perMinute(10)->by($email.$request->ip());
        });
    }
}
