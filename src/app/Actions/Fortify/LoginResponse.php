<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Session;


class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (!$user->email_verified_at) {
            return redirect()->route('verification.notice');
        }

        return redirect('/');
    }
}