<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
   // 購入画面表示
    public function buy(Item $item)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        //売り切れながら購入画面に進ませない
        if ($item->status == 'sold') {
            return redirect()->route('items.index')
                            ->with('error', 'この商品はすでに購入されています');
        }

        $profile = $user->profile;

        // 商品ごとのセッションキーを作る（複数タブで競合しないように）
        $sessionKey = "purchase_address_{$item->id}";

        //セッションにデータがない場合だけプロフィールで初期化
        if (!session()->has($sessionKey)) {
            session([$sessionKey => [
                'postal_code' => $profile->postal_code,
                'address' => $profile->address,
                'building' => $profile->building,
            ]]);
        }

        //セッションから住所データを取得
        $addressData = session($sessionKey);

        return view('purchase.buy', compact('item', 'addressData', 'profile'));
    }

    // 住所変更画面表示
    public function change(Item $item)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $profile = $user->profile;

        $sessionKey = "purchase_address_{$item->id}";

        $addressData = session($sessionKey, [
            'postal_code' => $profile->postal_code,
            'address'     => $profile->address,
            'building'    => $profile->building,
        ]);

        return view('purchase.change', compact('addressData', 'item'));
    }

    // 住所変更処理
    public function updateAddress(AddressRequest $request, Item $item)
    {
        // AddressRequestでバリデーション済み
        $validated = $request->validated();

        // 商品ごとにセッションを分けて保存（複数タブでの競合を防ぐ）
        $sessionKey = "purchase_address_{$item->id}";
        session([$sessionKey => $validated]);

        return redirect()->route('purchase.buy', $item->id);
    }

    // 購入処理
    public function store(PurchaseRequest $request, Item $item)
    {

        if (!Auth::check()) {
        dd('認証されていません');
    }
        $user = Auth::user();

        $validated = $request->validated();

        $sessionKey = "purchase_address_{$item->id}";

        // セッションの住所を使用（なければプロフィール）
        $addressData = session($sessionKey, [
            'postal_code' => $user->profile->postal_code,
            'address'     => $user->profile->address,
            'building'    => $user->profile->building,
        ]);

        Order::create([
            'user_id'           => $user->id,
            'item_id'           => $item->id,
            'payment_method'    => $request->payment_method,
            'recipient_name'    => $user->name,
            'recipient_postal'  => $addressData['postal_code'],
            'recipient_address' => $addressData['address'],
            'recipient_building'=> $addressData['building'],
        ]);

        $item->update(['status' => 'sold']);

        // セッションクリア（購入済みの商品だけ）
        session()->forget($sessionKey);

        return redirect('/')->with('success', '購入が完了しました');
    }


}
