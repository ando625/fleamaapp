<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\facades\Session;
// セッション（ユーザーごとの一時記憶領域）を操作する魔法箱

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 新規登録直後なら profile.edit に飛ばす
        if (Session::pull('newly_registered', false)) {
            return redirect()->route('profile.edit');
        }

    

        return redirect('/'); // 通常はトップページ
    }
}