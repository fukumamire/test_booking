<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ShopImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;


class ShopController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:admin'); // 管理者認証ミドルウェア
  }

// インポート処理
  public function import(Request $request)
  {
    $request->validate([
      'file' => 'required|mimes:xlsx,xls,csv|max:2048'
    ]);

  // インポート処理
    $import = new ShopImport();
    Excel::import($import, $request->file('file'));

    // 処理完了後のリダイレクト
    return redirect()->back()->with('success', 'Shops imported successfully.');
  }
 // インポートフォーム表示
  public function importForm()
  {
    return view('admin.shops.import');
  }
}
