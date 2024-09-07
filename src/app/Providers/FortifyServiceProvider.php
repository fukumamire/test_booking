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
   */
  public function register(): void
  {
    $this->app->instance(LogoutResponse::class, new class implements LogoutResponse
    {
      public function toResponse($request)
      {
        return redirect('/login');
      }
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //一般ユーザ―用
    // 新規ユーザーの登録処理
    Fortify::createUsersUsing(CreateNewUser::class);

    // Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    // Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

    // ユーザー登録画面を表示するためのビュー。
    Fortify::registerView(function () {
      return view('auth.register');
    });

    // ユーザーログイン画面を表示するためのビューを指定します。
    // 'auth.login'は、resources/views/auth/login.blade.phpに対応します。
    Fortify::loginView(function () {
      return view('auth.login');
    });

    $this->app->singleton(RegisterResponse::class, CustomRegisterResponse::class);

    RateLimiter::for('login', function (Request $request) {
      $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

      return Limit::perMinute(1000)->by($throttleKey);
    });

    // 二要素認証のチャレンジビューをカスタマイズ
    Fortify::twoFactorChallengeView(function () {
      return view('auth.two-factor-challenge');
    });
    // RateLimiter::for('two-factor', function (Request $request) {
    //     return Limit::perMinute(5)->by($request->session()->get('login.id'));
    // });

    Fortify::authenticateUsing(function ($request) {
      $authGuard = $request->input('guard') === 'admin' ? 'web' : null;
      $guard = Auth::guard($authGuard);

      // ガードがSessionGuardであることを確認
      if ($guard instanceof \Illuminate\Auth\SessionGuard) {
        $user = $guard->getProvider()->retrieveByCredentials(
          $request->only(Fortify::username(), 'password')
        );

        // ユーザーが存在しない場合はnullを返す
        if (!$user) {
          return false;
        }

        if (
          $authGuard === 'web' && !$user->hasRole('super-admin')
        ) {
          return false; // 管理者としてログインしようとしたが、管理者権限を持っていない
        }

        $guard->setUser($user);
        return true; // 認証成功
      }

      // ガードがSessionGuardでない場合の処理
      return false;
    });
  }
}
