<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;


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

    // ---------------------------------------------------
    // boot() : ログイン・登録処理の流れを定義
    // ---------------------------------------------------
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


        Event::listen(Registered::class, function ($event) {
            $event->user->sendEmailVerificationNotification();
        });
    }
}
