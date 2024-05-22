<?php

use Illuminate\Support\Facades\Route;

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


// 会員登録後　リダイレクト先を指定　CustomRegisterResponseがあるため
Route::get('/thanks', function () {
    return view('auth.thanks');
});

// 予約完了後　コントローラーやアクションを経由せずに、すぐにビューを表示
Route::view('/done', 'done');


//会員登録画面を作成するために便宜　店舗一覧
Route::get('/', function () {
    return view('index');
})->name('home');

// Route::get('/mypage', function () {
//     return view('mypage.mypage');
// })->name('mypage');
