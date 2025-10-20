<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\PurchaseRequest;
use Stripe\Stripe;


class PurchaseController extends Controller
{
    public function buy(Item $item)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($item->status == 'sold') {
            return redirect()->route('items.index');
        }

        $profile = $user->profile;

        $sessionKey = "purchase_address_{$item->id}";

        if (!session()->has($sessionKey)) {
            session([$sessionKey => [
                'postal_code' => $profile->postal_code,
                'address' => $profile->address,
                'building' => $profile->building,
            ]]);
        }

        $addressData = session($sessionKey);

        return view('purchase.buy', compact('item', 'addressData', 'profile'));
    }

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

    public function updateAddress(AddressRequest $request, Item $item)
    {
        $validated = $request->validated();

        $sessionKey = "purchase_address_{$item->id}";
        session([$sessionKey => $validated]);

        return redirect()->route('purchase.buy', $item->id);
    }

    public function checkoutStore(Item $item, PurchaseRequest $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        $paymentMethod = $request->input('payment_method', 'card');

        if ($paymentMethod === 'konbini') {
            Order::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'payment_method' => 'convenience_store',
                'recipient_name' => $user->name,
                'recipient_postal' => $user->profile->postal_code,
                'recipient_address' => $user->profile->address,
                'recipient_building' => $user->profile->building,
            ]);

            $item->update(['status' => 'sold']);

            return redirect('/')->with('success', 'コンビニ支払いで購入が完了しました！');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $successUrl = route('purchase.complete', $item->id);
        $cancelUrl  = route('items.index');

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
        ]);

        return redirect($session->url);
    }

    public function completeStore(Item $item, Request $request)
    {
        $user = Auth::user();
        if (!$user) return redirect()->route('login');

        if ($item->status === 'sold') {
            return redirect('/')->with('error', 'この商品はすでに購入されています');
        }

        Order::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'stripe',
            'recipient_name' => $user->name,
            'recipient_postal' => $user->profile->postal_code,
            'recipient_address' => $user->profile->address,
            'recipient_building' => $user->profile->building,
        ]);

        $item->update(['status' => 'sold']);

        return redirect('/')->with('success', 'カード支払いで購入が完了しました！');
    }


}
