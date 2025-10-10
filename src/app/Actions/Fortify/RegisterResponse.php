<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Support\Facades\Session;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {

        // プロフィール編集画面へ
        session()->flash('newly_registered', true);
        return redirect()->route('profile.create');
    }
}