<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// ====================
// 公開ルート
// ====================

// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');


// 商品詳細（動的ルートは静的の後！）
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');


// ====================
// 認証が必要なルート
// ====================
Route::middleware('auth')->group(function () {

    // マイページ
    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    // プロフィール
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/create', [ProfileController::class, 'create'])->name('create');
        Route::post('/store', [ProfileController::class, 'store'])->name('store');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
    });

    // 出品（POST）
    Route::get('/sell', [ItemController::class, 'listing'])->name('items.listing');
    Route::post('/sell', [ItemController::class, 'storeListing'])->name('items.store');

    // いいね・コメント
    Route::post('/items/{item}/favorite', [ItemController::class, 'favorite'])->name('items.favorite');
    Route::delete('/items/{item}/favorite', [ItemController::class, 'unfavorite'])->name('items.unfavorite');
    Route::post('/items/{item}/comment', [ItemController::class, 'storeComment'])->name('items.comment');

    // 購入
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/{item}', [PurchaseController::class, 'buy'])->name('buy');
        Route::post('/{item}/store', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{item}/address', [PurchaseController::class, 'change'])->name('change');
        Route::post('/{item}/address', [PurchaseController::class, 'updateAddress'])->name('updateAddress');
    });
});