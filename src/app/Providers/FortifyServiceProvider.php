<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register()
    {
        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Actions\Fortify\LoginResponse::class
        );

        $this->app->singleton(
            \Laravel\Fortify\Contracts\RegisterResponse::class,
            \App\Actions\Fortify\RegisterResponse::class
        );
    }

    public function boot()

    {

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });


        Fortify::authenticateUsing(function (Request $request) {

            $loginRequest = new LoginRequest();

            $rules = $loginRequest->rules();
            $messages = $loginRequest->messages();

            $request->validate($rules, $messages);

            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                return $user;
            }

            return null;
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        // config/fortify.php の limiters で指定している回数制限を定義する
        // （Laravel 11 からは、定義されていない名前を指定するとエラーになる）
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

    }
}
