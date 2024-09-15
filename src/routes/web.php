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

use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Auth\RegisterController;
use
  App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;

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

// 一般ユーザー会員登録ページ
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');


// 一般ユーザー　ログインページ
Route::get('/login', [LoginController::class, 'showLoginForm'])
  ->middleware('guest')
  ->name('login');

Route::post('/login', [LoginController::class, 'login'])
  ->middleware('guest')
  ->name('login.submit');

// 一般ユーザー　ログアウト
Route::post('/logout', [LoginController::class, 'logout'])
  ->middleware('auth')
  ->name('logout');

//　コントローラ等使用せず　会員登録、ログインを促すページ
Route::view('/request_login', 'auth.request_login')->name('request_login');


// 会員登録後　リダイレクト先を指定　CustomRegisterResponseがあるため
Route::get('/thanks', function () {
  return view('auth.thanks');
})->name('thanks');;

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

Route::group(['prefix' => 'admin'], function () {
  // 管理者ログイン
  Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
  Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

  // 管理者ログアウト
  Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

  // 管理者登録
  Route::get('/register', [AdminRegisterController::class, 'showRegistrationForm'])->middleware(['guest'])->name('admin.register');
  Route::post('/register', [AdminRegisterController::class, 'store'])->middleware(['guest'])->name('admin.register.submit');

  // 認証済み管理者のみアクセス可能なルート
  Route::group(['middleware' => ['auth:admin']], function () {
    // 管理者ホームページ
    Route::get('/index', function () {
      return view('admin.index');
    })->name('admin.index');

    // ユーザー管理
    Route::resource('users', UsersController::class)->except(['show']);

    Route::get('/create-shop-manager', [UsersController::class, 'createShopManager'])->name('users.create-shop-manager');
    Route::post('/store-shop-manager', [UsersController::class, 'storeShopManager'])->name('users.store-shop-manager');

    Route::view('/shop-manager-done', 'admin.users.shop-manager-done')->name('users.shop-manager-done');

    // 管理者　ユーザー一覧を取得するため
    Route::get('/user/index', [UsersController::class, 'index'])->name('admin.user.index');
  });
});


// 管理者用ルート
// Route::group(
//   ['prefix' => 'admin'],
//   function () {
// Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');

// //管理者登録画面表示　ゲストのみアクセス可能ルート

//   Route::get('/register', [AdminRegisterController::class, 'showRegistrationForm'])->middleware(['guest'])->name('admin.register');

//   //管理者保存ルート
//   Route::post('/register', [AdminRegisterController::class, 'store'])->middleware(['guest'])->name('admin.register.submit');
//   });

//   // 管理者登録済み（認証済み）ユーザーのみアクセス可能なルート
// Route::group(['middleware' => ['auth']], function () {
//     // 管理者用ホームページ画面の表示
//     Route::get('/admin/index', 'admin.index')->name('admin.index');

//     //店舗代表者作成関係
//     Route::resource('users', UsersController::class)->except(['show']); //showアクション（通常は個々のユーザーの詳細を表示するためのもの）を除外

//     Route::get('/create-shop-manager', [UsersController::class, 'createShopManager'])->name('users.create-shop-manager');

//     Route::post('/store-shop-manager', [UsersController::class, 'storeShopManager'])->name('users.store-shop-manager');

//     // 店舗代表者登録完了画面
//     Route::view('/shop-manager-done', 'admin.users.shop-manager-done')->name('users.shop-manager-done');
//   });



// 管理者専用ホームページ（admin.index）便宜作成

// Route::get('/admin/index', function () {
//   return view('admin.index');
// });

// 管理者　ユーザー一覧を表示するためのデータ取得
// Route::get('/admin/user/index', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin.user.index');
