<?php

namespace App\Actions\Fortify;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class LogoutAction implements AuthenticatedSessionController
{
  public function destroy(Request $request): LogoutResponse
  {
    $guard = Auth::guard()->check() ? 'web' : 'admin';

    Auth::guard($guard)->logout();

    $request->session()->invalidate();

    $response = redirect('/');
    Auth::logoutOtherDevices($request?->password);

    return app(LogoutResponse::class)->to($response);
  }
}
