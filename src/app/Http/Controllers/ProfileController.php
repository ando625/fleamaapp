<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Transaction;



class ProfileController extends Controller
{
    public function mypage(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('page', 'sell');
        $listings = $user->items()->get();


        $purchases = Transaction::where('buyer_id', $user->id)
            ->whereNotNull('completed_at')
            ->whereHas('item', function ($q) {
                $q->where('status', 'sold');
            })
            ->with('item')
            ->get()
            ->map(fn($t) => $t->item);

        $transactions = Transaction::where(function ($q) use ($user){
            $q->where('seller_id', $user->id)
                ->orWhere('buyer_id', $user->id);
        })
            ->whereNull('completed_at')
            ->with(['item', 'messages'])
            ->withMax('messages', 'created_at')
            ->orderBy('messages_max_created_at', 'desc')
            ->get()
            ->unique('id');

        $transactions->each(function ($transaction) use ($user) {
            $transaction->unread_count = $transaction->messages
                ->where('is_read', false)
                ->where('sender_id', '!=', $user->id)
                ->count();
        });

        $transactionCount = $transactions
            ->filter(function ($transaction) {
                return $transaction->unread_count > 0;
            })
            ->count();


        if ($tab === 'sell') {
            $itemsToShow = $listings;
        } elseif ($tab === 'buy') {
            $itemsToShow = $purchases;
        } else {
            $itemsToShow = $transactions;
        }


        return view('profile.profile', compact('user', 'listings', 'purchases', 'transactions','transactionCount', 'tab', 'itemsToShow'));
    }

    public function listings()
    {
        $user = Auth::user();
        $tab = 'sell';
        $listings =$user->items;

        return view('profile.profile', compact('user', 'tab', 'listings'));
    }

    public function purchases()
    {
        $user = Auth::user();
        $tab = 'buy';
        $purchases = Item::whereIn('id', $user->orders()->pluck('item_id'))->get();

        return view('profile.profile', compact('user', 'tab', 'purchases'));
    }

    public function create()
    {
        $user = Auth::user();

        return view('profile.create', compact('user'));
    }

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

    public function edit()
    {
        $user = Auth::user();

        $profile = $user->profile ?? new Profile();

        return view('profile.edit', compact('user', 'profile'));
    }


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