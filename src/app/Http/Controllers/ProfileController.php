<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Item;

class ProfileController extends Controller
{
    //マイページ表示
    public function mypage(Request $request)
    {
        $user = Auth::user();

        $tab = $request->query('page', 'sell');

        $listings = $user->items()->get();

        $purchases = Item::whereIn('id', $user->orders()->pluck('item_id'))->get();

        return view('profile.profile', compact('user', 'listings', 'purchases', 'tab'));
    }

    //出品商品タブ（画面切り替え）
    public function listings()
    {
        $user = Auth::user();
        $tab = 'sell';
        $listings =$user->items;

        return view('profile.profile', compact('user', 'tab', 'listings'));
    }

    //購入商品タブ（画面切り替え）
    public function purchases()
    {
        $user = Auth::user();
        $tab = 'buy';
        $purchases = Item::whereIn('id', $user->orders()->pluck('item_id'))->get();

        return view('profile.profile', compact('user', 'tab', 'purchases'));
    }

    // 新規プロフィール作成フォーム表示
    public function create()
    {
        $user = Auth::user();

        return view('profile.create', compact('user'));
    }

    //新規プロフィール保存
    public function store(ProfileRequest $request)
    {
        $user = Auth::user();

        $profileImage = $request->file('profile_image')
                        ? $request->file('profile_image')->store('profiles', 'public')
                        : null;

        $user->update([
            'name' => $request->name
        ]);

        $user->profile()->create([
            'profile_image' => $profileImage,
            'postal_code'   => $request->postal_code,
            'address'       => $request->address,
            'building'      => $request->building,
        ]);



        return redirect('/');
    }

    //既存プロフィール編集フォーム表示
    public function edit()
    {
        $user = Auth::user();

        $profile = $user->profile ?? new Profile();

        return view('profile.edit', compact('user', 'profile'));
    }

    //既存プロフィール更新
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $profileImage = $request->file('profile_image')
                        ? $request->file('profile_image')->store('profiles', 'public')
                        : $user->profile->profile_image ?? null;

        $user->update([
            'name' => $request->name
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_image' => $profileImage,
                'postal_code'   => $request->postal_code,
                'address'       => $request->address,
                'building'      => $request->building,
            ]
        );

        foreach (session()->all() as $key => $value) {
            if (str_starts_with($key, 'purchase_address_')) {
                session()->forget($key);
            }
        }

        return redirect()->route('mypage');
    }
}