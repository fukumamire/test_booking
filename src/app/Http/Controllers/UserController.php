<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function checkLoginStatus(Request $request)
  {
    return response()->json(['isLoggedIn' => !is_null($request->user())]);
  }
}
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;

// class UserController extends Controller
// {
//   public function checkLoginStatus(Request $request)
//   {
//     return response()->json(['isLoggedIn' => Auth::check()]);
//   }
  
// }
