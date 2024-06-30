<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function checkLoginStatus()
  {
    return response()->json(['isLoggedIn' => Auth::check()]);
  }
}
