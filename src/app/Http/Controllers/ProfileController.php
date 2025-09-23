<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;       
use App\Http\Requests\ProfileRequest;      


class ProfileController extends Controller
{
    //
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->update(['name' => $request->name]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_image' => $request->profile_image,
                'postal_code'   => $request->postal_code,
                'address'       => $request->address,
                'building'      => $request->building,
            ]
        );

        return redirect()->route('profile.edit');
    }
}
