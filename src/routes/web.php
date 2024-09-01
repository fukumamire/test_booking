<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Models\User;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;

use App\Services\QrCodeService;
use App\Http\Controllers\QrCodeController;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AdminAuthController;

use App\Http\Controllers\Admin\UsersController;


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

// 予約完了後　コントローラーやアクションを経由せずに、すぐにビューを表示　予約完了の画面へ
Route::view('/done', 'done')->name('done');


//店舗一覧
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

// レビューフォーム（入力）を表示するルート
Route::get('/review/{shop}/create', [ReviewController::class, 'create'])->name('review.create');

// 評価　レビューを保存
Route::post('/review/store', [ReviewController::class, 'store'])->name('review.store');

//店舗のレビューページ
Route::get('/shop/{shop}/reviews', [ShopController::class, 'showReviews'])->name('shop.reviews');


//2要素認証　
Route::get('/two-factor-challenge', function () {
    return view('auth.two-factor-challenge');
})->middleware(['auth', 'verified'])->name('two-factor.login');

// QRコード
Route::get('/reservation/qrcode/{bookingId}', [QrCodeController::class, 'generateQrCode'])->name('reservation.qrcode');

// QRコードがスキャンされた後に実行される処理
Route::get('/reservation/scan', [QrCodeController::class, 'authenticateReservation'])->name('reservation.scan');

// 店舗側　QRコード 予約認証　成功　予約完了
Route::get('/reservation/success', function () {
    return view('reservation_success');
})->name('reservation.success');

// 店舗側　予約認証に失敗時
Route::get('/reservation/failure', function () {
    return view('reservation_failure');
})->name('reservation.failure');


// 管理者登録
Route::get('/admin/register', function () {
    return view('admin.auth.register');
})->middleware(['guest']);

Route::post('/admin/register', [App\Http\Controllers\Auth\AdminRegisterController::class, 'store'])->middleware(['guest']);


// 管理者用ログインルート
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');

//管理者用　店舗代表者作成など
Route::group(['prefix' => 'admin', 'middleware' => ['auth']], function () {
    Route::resource('users', UsersController::class)->except(['show']);

    Route::get('/create-shop-manager', [UsersController::class, 'createShopManager'])->name('users.create-shop-manager');

    Route::post('/store-shop-manager', [UsersController::class, 'storeShopManager'])->name('users.store-shop-manager');

    // 店舗代表者登録完了画面
    Route::view('/shop-manager-done', 'admin.users.shop-manager-done')->name('users.shop-manager-done');

});
