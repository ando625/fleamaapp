<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Validator;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   // ---------------------------------------------------
    // register() : レスポンスを自作に置き換える
    // ---------------------------------------------------
    public function register()
    {
        // ログイン後のレスポンスを自作クラスに
        $this->app->singleton(
            \Laravel\Fortify\Contracts\LoginResponse::class,
            \App\Actions\Fortify\LoginResponse::class
        );

        // 登録後のレスポンスも自作クラスに
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

        RateLimiter::for('login', function () {
            return Limit::none();
        });



        // 🔹 ログイン画面のビューを指定
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // 🔹 新規登録画面のビューを指定
        Fortify::registerView(function () {
            return view('auth.register');
        });
    
        // ログイン処理のカスタム
        Fortify::authenticateUsing(function (Request $request) {
            
            // ① LoginRequest の rules と messages を取得
            $loginRequest = new LoginRequest(); // インスタンス化

            $rules = $loginRequest->rules();
            $messages = $loginRequest->messages();

            // ② 明示的にバリデーション
            $request->validate($rules, $messages);


            // メールでユーザー検索
            $user = \App\Models\User::where('email', $request->email)->first();

            // ユーザーが存在してパスワードが合えばログイン成功
            if ($user && Auth::attempt([
                'email' => $request->email,
                'password' => $request->password,
            ])) {
                return $user; // ログイン成功
            }

            return null; // ログイン失敗
        });

        //  登録処理はCreateNewUserに丸投げ
        Fortify::createUsersUsing(CreateNewUser::class);
    }
}
