<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Requests\MessageRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Review;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;


class TransactionController extends Controller
{
    // 取引画面
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        // この取引に紐づく商品（order → item）
        $item = $transaction->item;

        // 取引相手を判定
        if ($user->id === $transaction->seller_id) {
            //自分が出品者->相手は購入者
            $partner = $transaction->buyer;
        } else {
            //自分が購入者->相手は出品者
            $partner = $transaction->seller;
        }

        //今表示してる商品ID
        $currentItemId = $item->id;

        $otherTransactions = Transaction::where(function ($q) use ($user) {
            $q->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id);
        })
            ->whereNull('completed_at')          // ← 完了は除外
            ->where('id', '!=', $transaction->id) // ← 今の取引を除外
            ->with('item')
            //この↓と２行はメッセージ新着順に並べるか、エラーが出るなら消してよし
            ->withMax('messages', 'created_at')
            ->orderBy('messages_max_created_at', 'desc')
            ->get();


        // メッセージ（古い順）
        $messages = $transaction->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        // 自分以外が送った未読メッセージを既読にする
        $transaction->messages()
            ->where('is_read', false)
            ->where('sender_id', '!=', $user->id)
            ->update(['is_read' => true]);

        return view('profile.interact', compact('transaction', 'item', 'messages', 'user', 'partner', 'currentItemId', 'otherTransactions'));
    }

    //メッセージ投稿
    public function messageStore(MessageRequest $request, Transaction $transaction)
    {
        $user = Auth::user();

        //画像保存
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

        //自分以外のメッセージは拒否
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

    //星の評価送信
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

        // 購入者が評価
        if ($user->id === $transaction->buyer_id) {
            $transaction->buyer_reviewed = true;
        }

        // 出品者が評価
        if ($user->id === $transaction->seller_id) {
            $transaction->seller_reviewed = true;
        }

        // 両方評価済みなら取引完了
        if ($transaction->isBothReviewed()) {
            $transaction->completed_at = now();
            $transaction->status = 'completed';

            $transaction->item->update([
                'status' => 'sold',
            ]);

            // ★ 出品者へメール送信
            Mail::to($transaction->seller->email)
                ->send(new TransactionCompletedMail($transaction));
        }

        $transaction->save();


        return redirect()->route('items.index');
    }

    
}
