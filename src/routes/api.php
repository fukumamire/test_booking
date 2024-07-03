<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\LogoutController;
/*
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 保護したいルートにauth:sanctumミドルウェアを適用
Route::middleware('auth:sanctum')->get('/check-login-status', [UserController::class, 'checkLoginStatus']);

// Route::get('/check-login-status', [UserController::class, 'checkLoginStatus']);


Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/shops/{shop}/is-favorite', [ShopController::class, 'isFavorite']);
