<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Requests\MessageRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;


class TransactionController extends Controller
{
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        $item = $transaction->item;

        if ($user->id === $transaction->seller_id) {
            $partner = $transaction->buyer;
        } else {
            $partner = $transaction->seller;
        }

        $currentItemId = $item->id;

        $otherTransactions = Transaction::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })
            ->whereNull('completed_at')
            ->where('id', '!=', $transaction->id)
            ->with('item')
            ->withMax('messages', 'created_at')
            ->orderBy('messages_max_created_at', 'desc')
            ->get();


        $messages = $transaction->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        $transaction->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        return view('profile.interact', compact('transaction', 'item', 'messages', 'user', 'partner', 'currentItemId', 'otherTransactions'));
    }

    public function messageStore(MessageRequest $request, Transaction $transaction)
    {
        $user = Auth::user();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('message', 'public');
        }

        Message::create([
            'transaction_id' => $transaction->id,
            'sender_id' => $user->id,
            'body' => $request->body,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        return redirect()->back()->withInput();

    }

    public function messageUpdate(MessageRequest $request, Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $message->update([
            'body' => $request->body,
        ]);

        return redirect()->back();
    }

    public function messageDestroy(Message $message)
    {
        $user = Auth::user();

        if ($message->sender_id !== $user->id) {
            abort(403);
        }

        $message->delete();

        return redirect()->back();
    }

    public function rate(Request $request, Transaction $transaction)
    {
        $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        $user = Auth::user();

        $ratedUserId =
            $user->id === $transaction->buyer_id
            ? $transaction->seller_id
            : $transaction->buyer_id;

        Review::create([
            'transaction_id' => $transaction->id,
            'reviewer_id' => $user->id,
            'reviewee_id' => $ratedUserId,
            'rating' => $request->rating,
        ]);

        if ($user->id === $transaction->buyer_id) {
            $transaction->buyer_reviewed = true;
        }

        if ($user->id === $transaction->seller_id) {
            $transaction->seller_reviewed = true;
        }

        if ($transaction->isBothReviewed()) {
            $transaction->completed_at = now();
            $transaction->status = 'completed';

            $transaction->item->update([
                'status' => 'sold',
            ]);

            Mail::to($transaction->seller->email)
                ->send(new TransactionCompletedMail($transaction));
        }

        $transaction->save();


        return redirect()->route('items.index');
    }

}
