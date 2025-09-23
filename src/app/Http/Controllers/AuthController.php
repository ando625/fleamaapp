<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    public function index() {

        return view('index');
    }

    // ログインフォームを表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(LoginRequest $request)
    {
        // LoginRequest のバリデーションが自動で実行される
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // 認証成功 → ホームへリダイレクト
            return redirect()->intended('/');
        }

        // 認証失敗 → エラーメッセージを表示
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }
}
