<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BookingController;

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

// 会員登録ページ
Route::get('/register', function () {
    return view('auth.register');
})->middleware('guest')->name('register');

// ログインページ
Route::get('/login', function () {
    return view('auth.login');
})->middleware('guest')->name('login');

//　コントローラ等使用せず　会員登録、ログインを促すページ
Route::view('/request_login', 'auth.request_login')->name('request_login');


// 会員登録後　リダイレクト先を指定　CustomRegisterResponseがあるため
Route::get('/thanks', function () {
    return view('auth.thanks');
});

// 予約完了後　コントローラーやアクションを経由せずに、すぐにビューを表示
Route::view('/done', 'done')->name('done');


//会員登録画面　店舗一覧
Route::get('/', function () {
    return view('index');
})->name('home');


Route::get('/', [ShopController::class, 'index'])->name('shops.index');

// 検索機能
Route::get('/shops/search', [ShopController::class, 'search'])->name('shops.search');



// マイページ関係

Route::get('/mypage', function () {
    return view('mypage.my_page');
})->name('mypage');

// ログインしているユーザーのみがマイページにアクセスできるようにする
Route::get('/mypage', [BookingController::class, 'showMyPage'])->name('mypage')->middleware('auth');


Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

// 予約変更
Route::put('/bookings/{booking}/update', [BookingController::class, 'update'])->name('bookings.update');


// 予約をキャンセルするルートの定義
Route::delete('/bookings/{booking}/cancel', [BookingController::class, 'destroy'])->name('bookings.cancel');


//2024/6/17店舗詳細＆予約画面
Route::get('/detail/{shop}', [ShopController::class, 'detail'])->name('shop.detail');

Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');
