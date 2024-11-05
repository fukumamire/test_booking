<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ShopImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;


class ShopController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:admin'); // 管理者認証ミドルウェア
  }

  public function import(Request $request)
  {
    try {
      $filePath = $request->file('file')->getPathName();
      $extension = $request->file('file')->getClientOriginalExtension();

      switch ($extension) {
        case 'xlsx':
          $readerType = \Maatwebsite\Excel\Excel::XLSX;
          break;
        case 'xls':
          $readerType = \Maatwebsite\Excel\Excel::XLS;
          break;
        case 'csv':
          $readerType = \Maatwebsite\Excel\Excel::CSV;
          break;
        default:
          throw new \Exception("Unsupported file type: {$extension}");
      }

      Excel::import(new ShopImport(), $request->file('file'), null, $readerType);

      return redirect()->back()->with('success', 'Shops imported successfully.');
    } catch (\Exception $e) {
      Log::error($e->getMessage());
      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
  }

  public function importForm()
  {
    return view('admin.shops.import');
  }
}
// class ShopController extends Controller
// {
//   public function __construct()
//   {
//     $this->middleware('auth:admin'); // 管理者認証ミドルウェア
//   }
//   public function import(Request $request)
//   {
    // $request->validate([
    //   'file' => [
    //     'required',
    //     'mimes:xlsx,xls,csv',
    //     'max:2048', // ファイルサイズ制限（2MB）
    //   ],
    // ], [
    //   'file.required' => 'ファイルを選択してください',
    //   'file.mimes' => '有効なファイル形式は.xlsx、.xls、または.csvのみです',
    //   'file.max' => 'ファイルサイズは2MB以内でなければなりません',
    // ]);

    // 

  //   try {
  //     $filePath = $request->file('file')->getRealPath(); // ファイル名を 'file' に変更

  //     $collection = Excel::toCollection(new ShopImport, $filePath);

  //     $import = new ShopImport();
  //     Excel::import($import, $request->file('file'));

  //     return redirect()->back()->with('success', 'Shops imported successfully.');
  //   } catch (\Exception $e) {

  //     return redirect()->back()->withErrors(['error' => $e->getMessage()]);
  //   }
  // }
  // インポート処理
  // public function import(Request $request)
  // {
  //   $request->validate([
  //     'file' => 'required|mimes:xlsx,xls,csv|max:2048'
  //   ]);

  // // インポート処理
  //   $import = new ShopImport();
  //   Excel::import($import, $request->file('file'));

  //   // 処理完了後のリダイレクト
  //   return redirect()->back()->with('success', 'Shops imported successfully.');
  // }

  // インポートフォーム表示
//   public function importForm()
//   {
//     return view('admin.shops.import');
//   }
// }
