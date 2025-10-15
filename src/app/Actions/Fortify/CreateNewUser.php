<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */

    public function create(array $input)
{
    $validator = Validator::make($input, 
        app(\App\Http\Requests\RegisterRequest::class)->rules(),
        app(\App\Http\Requests\RegisterRequest::class)->messages()
    );

    $validator->after(function ($validator) use ($input) {
        if (($input['password'] ?? null) !== ($input['password_confirmation'] ?? null)) {
            $validator->errors()->add('password_confirmation', 'パスワードと一致しません');
        }
    });

    $validator->validate();

    return User::create([
        'name' => $input['name'],
        'email' => $input['email'],
        'password' => Hash::make($input['password']),
    ]);
}
}
