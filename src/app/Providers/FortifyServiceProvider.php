<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use App\Http\Requests\LoginRequest as MyLoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------------------------
        // ユーザー登録処理
        // ---------------------------
        // CreateNewUser クラスを通して登録
        // RegisterRequest のバリデーションを利用する
        Fortify::createUsersUsing(CreateNewUser::class);

        // 登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });




        // ---------------------------
        // ログイン処理
        // ---------------------------
        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
        // 認証処理だけ行う
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
        return Auth::user();
        }

        throw ValidationException::withMessages([
        'email' => ['ログイン情報が登録されていません'],
        ]);
        });




        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン試行制限（1分に10回）
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($email.$request->ip());
        });

        // 初回登録後、プロフィール設定画面へリダイレクト
        Event::listen(Registered::class, function ($event) {
            return redirect()->route('profile.edit');
        });
    }
}
