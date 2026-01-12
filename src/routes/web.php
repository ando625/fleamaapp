<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\TransactionController;
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



Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/email/verify', function () {
    return view('auth.mail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.create');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送信しました');
})->middleware(['auth', 'throttle:100,1'])->name('verification.send');


Route::middleware('auth', 'verified')->group(function () {

    Route::get('/mypage', [ProfileController::class, 'mypage'])->name('mypage');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/create', [ProfileController::class, 'create'])->name('create');
        Route::post('/store', [ProfileController::class, 'store'])->name('store');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/interact/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::post('/interact/{transaction}/messages', [TransactionController::class, 'messageStore'])->name('messages.store');
        Route::put('/interact/messages/{message}', [TransactionController::class, 'messageUpdate'])->name('messages.update');
        Route::delete('/interact/messages/{message}', [TransactionController::class, 'messageDestroy'])->name('messages.destroy');
        Route::post('/interact/{transaction}/rate', [TransactionController::class, 'rate'])->name('transactions.rate');
        Route::post('/transactions/{transaction}/complete',[TransactionController::class, 'complete'])->name('transactions.complete');
    });

    Route::get('/sell', [ItemController::class, 'listing'])->name('items.listing');
    Route::post('/sell', [ItemController::class, 'storeListing'])->name('items.store');

    Route::post('/items/{item}/favorite', [ItemController::class, 'favorite'])->name('items.favorite');
    Route::delete('/items/{item}/favorite', [ItemController::class, 'unfavorite'])->name('items.unfavorite');
    Route::post('/items/{item}/comment', [ItemController::class, 'storeComment'])->name('items.comment');

    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/{item}/buy', [PurchaseController::class, 'buy'])->name('buy');
        Route::get('/{item}/change', [PurchaseController::class, 'change'])->name('change');
        Route::post('/{item}/updateAddress', [PurchaseController::class, 'updateAddress'])->name('updateAddress');

        Route::post('/{item}/checkout', [PurchaseController::class, 'checkoutStore'])->name('checkout');
        Route::get('/{item}/stripe-success', [PurchaseController::class, 'stripeSuccess'])->name('stripeSuccess');
    });

});