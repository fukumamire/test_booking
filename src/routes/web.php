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


//会員登録画面を作成するために便宜　店舗一覧
Route::get('/', function () {
    return view('index');
})->name('home');


Route::get('/', [ShopController::class, 'index'])->name('shops.index');
// 検索機能
Route::get('/shops/search', [ShopController::class, 'search'])->name('shops.search');


Route::put('/bookings/{booking}/update', [BookingController::class, 'update'])->name('bookings.update');
Route::post('/favorite/{shop}', [BookingController::class, 'favorite'])->name('favorite');
Route::delete('/unfavorite/{shop}', [BookingController::class, 'unfavorite'])->name('unfavorite');


// Route::post(
//     '/favorite/{shop_id}',
//     [ShopController::class, 'favorite']
// )->name('favorite');
// Route::delete('/unfavorite/{shop_id}', [ShopController::class, 'unfavorite'])->name('unfavorite');



//2024/6/17店舗詳細＆予約画面
Route::get('/detail/{shop}', [ShopController::class, 'detail'])->name('shop.detail');

Route::post('/bookings/store', [BookingController::class, 'store'])->name('bookings.store');

// 最終版？？予定
// Route::get('/detail/{shop_id}', [ShopController::class, 'detail'])->name('shop.detail');



// マイページ

Route::get('/mypage', function () {
    return view('mypage.my_page');
})->name('mypage');

Route::get('/mypage', [BookingController::class, 'showMyPage'])->name('mypage');

// Route::get('/mypage', function () {
//     return view('mypage.my_page');
// })->middleware('auth')->name('mypage');