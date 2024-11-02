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
use App\Http\Controllers\Admin\ShopManagerController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\AdminRegisterController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ShopManagerAuthController;
use App\Actions\Fortify\LogoutAction;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\EmailNotificationController;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

// 一般ユーザー用のメール認証通知ページ

// Route::get('/email/verify', function () {
//   Log::info("Accessing email verification page");
//   return view('auth.verify-email');
// })->middleware(['auth'])->name('verification.notice');


// Route::get('/email/verify', function () {
//   Log::info("Accessing email verification page");
//   return view('auth.verify-email');
// })->middleware(['auth'])->name('verification.notice');

// ユーザーがメールアドレスを確認していない状態で保護されたページにアクセスしようとした時のルート
Route::get('/email/verify', function () {
  return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//  認証リンククリック時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();

  return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// コントローラ等使用せず　会員登録、ログインを促すページ
Route::view('/request_login', 'auth.request_login')->name('request_login');

// 会員登録後　リダイレクト先を指定　CustomRegisterResponseがあるため
Route::get('/thanks', function () {
  return view('auth.thanks');
})->name('thanks');;

// 予約完了後　コントローラーやアクションを経由せずに、すぐにビューを表示　予約完了の画面へ
Route::view('/done', 'done')->name('done');

// 店舗一覧
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
Route::get('/mypage', [BookingController::class, 'showMyPage'])->name('mypage')->middleware(['auth', 'verified']);

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

  // 管理者登録
  Route::get('/register', [AdminRegisterController::class, 'showRegistrationForm'])->middleware(['guest'])->name('admin.register');
  Route::post('/register', [AdminRegisterController::class, 'store'])->middleware(['guest'])->name('admin.register.submit');

  // 管理者ログアウト
  Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

  // 認証済み管理者のみアクセス可能なルート
  Route::group(['middleware' => ['auth:admin']], function () {
    // 管理者ホームページ
    Route::get('/index', function () {
      return view('admin.index');
    })->name('admin.index');

    // ユーザー管理
    Route::resource('users', UsersController::class)->except(['show']);

    // 店舗代表者作成画面
    Route::get('/admin/shop-manager/register', [UsersController::class, 'createShopManager'])->name('admin.users.create-shop-manager');
    Route::get('/create-shop-manager', [UsersController::class, 'createShopManager'])->name('users.create-shop-manager');
    Route::post('/store-shop-manager', [UsersController::class, 'storeShopManager'])->name('users.store-shop-manager');

    Route::view('/shop-manager-done', 'admin.users.shop-manager-done')->name('users.shop-manager-done');

    // 管理者　ユーザー一覧を取得するため
    Route::get('/user/index', [UsersController::class, 'index'])->name('admin.user.index');
  });

  // 管理者用のメール認証ページ
  Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if ($user->markEmailAsVerified()) {
      // ユーザーの状態を直接更新
      $user->save();

      // 通知メッセージを表示
      return redirect()->route('admin.verification.notice')->with('success', 'メールアドレスの確認が完了しました。');
    }
    return back()->withErrors(['認証エラー']);
  })->middleware(['auth:admin', 'signed'])->name('admin.verification.verify');

  // 管理者用のメール認証通知ページ
  Route::get('/email/verify', function () {
    return view('admin.auth.verify-email');
  })->middleware(['auth:admin'])->name('admin.verification.notice');

  // 管理者用のメール認証リマインダー送信ページ 今回は使用しない
  // Route::post('/email/verification-notification', function (Request $request) {
  //   $user = Auth::guard('admin')->user();
  //   $user->notify(new VerifyEmail);
  //   return back()->with('message', 'メール認証リマインダーを送信しました。');
  // })->middleware(['auth:admin'])->name('admin.verification.send');
});

//ログアウト　一般ユーザーの両方
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
  ->name('logout')
  ->middleware('web');

// お知らせメール作成・送信
Route::get('/admin/email-notification', [EmailNotificationController::class, 'index'])->name('admin.email-notification');
Route::post('/admin/email-notification', [EmailNotificationController::class, 'store'])->name('admin.email-notification.store');

// 店舗代表者関係
Route::group(['prefix' => 'shop-manager'], function () {
  // ログイン関連のルート
  Route::get('/login', [ShopManagerAuthController::class, 'showLoginForm'])->name('shop-manager.login');
  Route::post('/login', [ShopManagerAuthController::class, 'login'])->name('shop-manager.login.submit');
  Route::post('/logout', [ShopManagerAuthController::class, 'logout'])->name('shop-manager.logout');

  // 認証済みのユーザー用のルート
  Route::middleware('auth:shop_manager')->group(function () {
    // shop_managerロールを持つユーザーのみがアクセスできるようにする

    Route::get('/dashboard', [ShopManagerController::class, 'dashboard'])->name('shop-manager.dashboard');
    Route::get('/shops/create', [ShopManagerController::class, 'createShop'])->name('shop-manager.shops.create');
    Route::post('/shops/store', [ShopManagerController::class, 'storeShop'])->name('shop-manager.shops.store');
    Route::get('/shops/{shop}/edit', [ShopManagerController::class, 'editShop'])->name('shop-manager.shops.edit');
    Route::patch('/shops/{shop}/update', [ShopManagerController::class, 'updateShop'])->name('shop-manager.shops.update');
    Route::get('/reservations', [ShopManagerController::class, 'reservations'])->name('shop-manager.reservations');
    // 店舗一覧
    Route::get('/shops', [ShopManagerController::class, 'index'])->name('shop-manager.shops.index');
    //店舗削除
    Route::delete('/shops/{shop}', [ShopManagerController::class, 'destroy'])->name('shop-manager.shops.destroy');
    //削除した店舗の復元
    Route::post('/shops/{shop}/restore', [ShopManagerController::class, 'restore'])->name('shop-manager.shops.restore');
  });
});
