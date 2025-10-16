<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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




// トップページ（商品一覧）
Route::get('/', [ItemController::class, 'index'])->name('items.index');


// 商品詳細
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');


// 認証誘導ページ
Route::get('/email/verify', function () {
    return view('auth.mail');
})->middleware('auth')->name('verification.notice');


// 認証リンク
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.create');
})->middleware(['auth', 'signed'])->name('verification.verify');



// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送信しました');
})->middleware(['auth', 'throttle:100,1'])->name('verification.send');


// ====================
// 認証が必要なルート
// ====================
Route::middleware('auth', 'verified')->group(function () {

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
        Route::get('/{item}/buy', [PurchaseController::class, 'buy'])->name('buy');
        Route::get('/{item}/change', [PurchaseController::class, 'change'])->name('change');
        Route::post('/{item}/updateAddress', [PurchaseController::class, 'updateAddress'])->name('updateAddress');
        Route::post('/{item}/store', [PurchaseController::class, 'store'])->name('store');

        //Stripe関係の追加ルート
        Route::post('/{item}/checkout', [PurchaseController::class, 'checkoutStore'])->name('checkout');
        Route::get('/{item}/complete', [PurchaseController::class, 'completeStore'])->name('complete');
    });

});


