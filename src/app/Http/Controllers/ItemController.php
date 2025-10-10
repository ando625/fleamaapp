<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;

class ItemController extends Controller
{
    // トップページ（おすすめ or マイリスト）表示
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');

        // キーワード取得・セッション保持
        if ($request->has('q')) {
            $q = $request->input('q');
            session(['search_q' => $q]);
        } elseif (session()->has('search_q')) {
            $q = session('search_q');
        } else {
            $q = null;
        }

        if ($tab === 'mylist') {
            // マイリストはログインユーザーのみ
            if (auth()->check()) {
                $query = Auth::user()->favorites()
                    ->with(['user', 'categories', 'condition'])
                    ->orderBy('favorites.created_at', 'desc');

                if (!empty($q)) {
                    $query->where('name', 'like', "%{$q}%");
                }

                $items = $query->get();
            } else {
                $items = collect(); // 未ログインは空
            }
        } else {
            // おすすめ（トップページ）はログイン不要で全件表示
            $query = Item::with(['user', 'categories', 'condition']);

            // ログインユーザーの場合、自分の出品は除外
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            if (!empty($q)) {
                $query->where('name', 'like', "%{$q}%");
            }

            $items = $query->get();
        }

        return view('index', compact('items', 'tab', 'q'));
    }

    // お気に入り追加処理
    public function favorite(Request $request, Item $item)
    {
        $user = Auth::user(); // ログイン中のユーザーを取得
        $user->favorites()->syncWithoutDetaching([$item->id]); 
        // syncWithoutDetaching:
        // 中間テーブル favorites に item_id を追加
        // すでに追加されていれば重複せず残る（detachされない）

        return back()->with('success', 'お気に入りに追加しました'); 
        // 前のページに戻る & メッセージをセッションに保存
    }


    // お気に入り削除処理
    public function unfavorite(Request $request, Item $item)
    {
        $user = Auth::user(); // ログイン中のユーザーを取得
        $user->favorites()->detach($item->id); 
        // detach(): 中間テーブルから item_id を削除する

        return back()->with('success', 'お気に入りを解除しました');
    }


    // 商品詳細画面表示
    public function show(Item $item)
    {
        // 関連データもまとめて取得して再取得
        $item = Item::with(['user', 'categories', 'condition', 'comments.user'])
                    ->findOrFail($item->id); // 該当商品がなければ404を返す

        return view('items.show', compact('item')); // items.show ブレードに $item を渡す
    }

    // コメント保存
    public function storeComment(CommentRequest $request, Item $item)
    {
        // バリデーション済みのデータだけを取得
        $validated = $request->validated();

        // コメントを保存
        $item->comments()->create([
            'user_id' => auth()->id(),    // コメントしたユーザーID
            'content' => $validated['content'], // 入力されたコメント内容
        ]);

        // コメント保存後、商品詳細画面にリダイレクト
        return redirect()->route('items.show', $item);
    }


    // 出品画面表示
    public function listing()
    {
        //カテゴリを全部取得
        $categories = Category::orderBy('id')->get();

        return view('listing', compact('categories')); // listing.blade.php を表示
    }


    //商品出品
    public function storeListing(ExhibitionRequest $request)
    {
        //バリデーション済みデータ取得
        $data = $request->validated();

        //画像アップロード
        if ($request->hasFile('item_path')) {
            $data['item_path'] = $request->file('item_path')->store('item','public');
        }

        //ログインユーザーのIDをセット
        $data['user_id'] = Auth::id();


        //itemモデルに＄dataをDBに保存
        $item = Item::create($data);

        // カテゴリーを多対多テーブルに保存
        $categoryIds = explode(',', $request->input('category_id'));  // 配列に変換
        $item->categories()->sync($categoryIds);

        return redirect('/')->with('success', '商品を出品しました');

    }
}