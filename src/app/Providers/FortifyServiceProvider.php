<?php

namespace App\Providers;

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
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {

  }

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

    // authenticateUsingメソッドは、カスタム認証ロジックを定義
    Fortify::authenticateUsing(function ($request) {
      $authGuard = $request->input('guard') === 'admin' ? 'web' : null;
      $guard = Auth::guard($authGuard);

      if ($guard instanceof \Illuminate\Auth\SessionGuard) {
        $user = $guard->getProvider()->retrieveByCredentials(
          $request->only(Fortify::username(), 'password')
        );

        if (!$user) {
          return false;
        }

        if (
          $authGuard === 'web' && !$user->hasRole('super-admin')
        ) {
          return false;
        }

        $guard->setUser($user);
        return true;
      }

      return false;
    });

    // loginViewメソッドは、ログインビューをカスタマイズ
    Fortify::loginView(function (Request $request) {
      if ($request->is('admin/*')) {
        return view('admin.auth.login');
      }
      return view('auth.login');
    });

    // ユーザー登録後に特定のリダイレクト処理（/thanks ページなどリダイレクト）を行うために使用
    $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);
  }
}
