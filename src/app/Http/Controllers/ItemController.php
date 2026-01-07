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
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'recommend');

        if ($request->has('q')) {
            $q = $request->input('q');
            session(['search_q' => $q]);
        } elseif (session()->has('search_q')) {
            $q = session('search_q');
        } else {
            $q = null;
        }

        if ($tab === 'mylist') {
            if (auth()->check()) {
                $query = Auth::user()->favorites()
                    ->with(['user', 'categories', 'condition'])
                    ->orderBy('favorites.created_at', 'desc');

                if (!empty($q)) {
                    $query->where('name', 'like', "%{$q}%");
                }

                $items = $query->get();
            } else {
                $items = collect();
            }
        } else {
            $query = Item::with(['user', 'categories', 'condition']);

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

    public function favorite(Request $request, Item $item)
    {
        $user = Auth::user();
        $user->favorites()->syncWithoutDetaching([$item->id]);

        return back();
    }


    public function unfavorite(Request $request, Item $item)
    {
        $user = Auth::user();
        $user->favorites()->detach($item->id);

        return back();
    }



    public function show(Item $item)
    {
        $item = Item::with(['user', 'categories', 'condition', 'comments.user'])
                    ->findOrFail($item->id);

        return view('items.show', compact('item'));
    }

    public function storeComment(CommentRequest $request, Item $item)
    {
        $validated = $request->validated();

        $item->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('items.show', $item);
    }


    public function listing()
    {
        $categories = Category::orderBy('id')->get();

        return view('listing', compact('categories'));
    }


    public function storeListing(ExhibitionRequest $request)
    {

        $data = $request->validated();

        if ($request->hasFile('item_path')) {
            $data['item_path'] = $request->file('item_path')->store('item','public');
        }

        $data['user_id'] = Auth::id();

        $item = Item::create($data);

        $categoryIds = explode(',', $request->input('category_id'));
        $item->categories()->sync($categoryIds);

        return redirect('/')->with('success', '商品を出品しました');

    }
}