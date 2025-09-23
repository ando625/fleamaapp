<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index() {

        //DBから全件取得
        $items = Item::all();


        return view('index', compact('items'));
    }
}
