<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Order;
use App\Models\Item;

class ProfileController extends Controller
{
    //マイページ表示
    public function mypage(Request $request)
    {
        $user = Auth::user();

        //?page=sell デフォルト
        $tab = $request->query('page', 'sell');

        //出品した商品
        $listings = $user->items()->get(); 

        // 購入した商品（Order経由でItemを取得
        $purchases = Item::whereIn('id', $user->orders()->pluck('item_id'))->get();

        return view('profile.profile', compact('user', 'listings', 'purchases', 'tab'));
    }

    //出品商品タブ（画面切り替え）
    public function listings()
    {
        $user = Auth::user();
        $tab = 'sell';  //Blade（ビュー）で「どのタブがアクティブか」を判定するための変数
        $listings =$user->items;  //ログインユーザーが出品した商品を取得

        return view('profile.profile', compact('user', 'tab', 'listings'));
    }

    //購入商品タブ（画面切り替え）
    public function purchases()
    {
        $user = Auth::user();
        $tab = 'buy';  //bladeで購入タブをアクティブにするため
        $purchases = Item::whereIn('id', $user->orders()->pluck('item_id'))->get();
        //ユーザーが購入した商品のみを取得 pluck('item_id') で、ユーザーの注文(Order)テーブルから購入した商品のIDだけ抜き出す,whereIn でそのIDに該当する商品(Item)をまとめて取得

        return view('profile.profile', compact('user', 'tab', 'purchases'));
    }

    // 新規プロフィール作成フォーム表示
    public function create()
    {
        $user = Auth::user();

        // 新規作成用フォームには既存のプロフィールがなくてもOK
        return view('profile.create', compact('user'));
    }

    //新規プロフィール保存
    public function store(ProfileRequest $request)
    {
        $user = Auth::user();


        // 画像アップロード処理
        $profileImage = $request->file('profile_image')
                        ? $request->file('profile_image')->store('profiles', 'public')
                        : null;


        // ユーザー名も更新
        $user->update([
            'name' => $request->name
        ]);

        // プロフィール新規作成
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

        // プロフィールが無ければ空のProfileオブジェクトを渡す
        $profile = $user->profile ?? new Profile();

        return view('profile.edit', compact('user', 'profile'));
    }

    //既存プロフィール更新
    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 画像アップロードがあれば上書き、なければ既存画像を使用
        $profileImage = $request->file('profile_image')
                        ? $request->file('profile_image')->store('profiles', 'public')
                        : $user->profile->profile_image ?? null;

        // ユーザー名更新
        $user->update([
            'name' => $request->name
        ]);

        // プロフィール更新（新規作成される心配なし、updateOrCreate でもOK）
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_image' => $profileImage,
                'postal_code'   => $request->postal_code,
                'address'       => $request->address,
                'building'      => $request->building,
            ]
        );

        return redirect()->route('mypage');
    }
}