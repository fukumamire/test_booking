<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Laravel\Fortify\Contracts\RegisterResponse;
use App\Http\Responses\CustomRegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {}

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  // bootメソッドは、サービスプロバイダが全てのサービスを登録した後に実行
  public function boot()
  {
    // 新規ユーザーの設定　新規ユーザー作成時にCreateNewUserクラスを使用
    Fortify::createUsersUsing(CreateNewUser::class);

    // メール認証の設定を追加
    Fortify::verifyEmailView(function () {
      return view('auth.verify-email');
    });

    // authenticateUsingメソッドは、カスタム認証ロジックを定義
    Fortify::authenticateUsing(function (Request $request) {
      // 管理者用の認証
      if (
        $request->input('guard') === 'admin'
      ) {
        $user = User::where(Fortify::username(), $request->input(Fortify::username()))->first();

        if ($user && $user->isAdmin() && Hash::check($request->input('password'), $user->password)) {
          return $user; // 認証成功時にユーザーを返す
        }
      } else {
        // 店舗代表者の認証
        $guard = Auth::guard('shop_manager');

        if ($guard->attempt($request->only(Fortify::username(), 'password'))) {
          return $guard->user(); // 認証成功時にユーザーを返す
        }
      }

      return null; // 認証失敗
    });

    // loginViewメソッドは、ログインビューをカスタマイズ
    Fortify::loginView(function (Request $request) {
      if ($request->is('admin/*')) {
        return view('admin.auth.login');
      }
      return view('auth.shop-manager-login');
    });

    // ユーザー登録後に特定のリダイレクト処理（/thanks ページなどリダイレクト）を行うために使用
    $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
  }
}
