<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ShopImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ShopController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:admin');
  }

  public function import(Request $request)
  {
    try {
      if (!$request->hasFile('file')) {
        Log::warning('No file uploaded');
        return back()->withError('Please select a file to upload');
      }

      $file = $request->file('file');
      Log::info('File uploaded: ' . $file->getClientOriginalName());

      // バリデーションをここで実行
      $this->validateRequest($request);

      $extension = $file->getClientOriginalExtension();

      // CSVファイルの内容をログに出力
      $content = file_get_contents($file->getRealPath());
      Log::info('CSV file content:', [$content]);

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
          throw new \InvalidArgumentException("Unsupported file type: {$extension}");
      }

      $import = new ShopImport();
      Excel::import($import, $file, null, $readerType);

      if (session()->has('import_errors')) {
        return redirect()->back()->withErrors(session()->get('import_errors'));
      }

      Log::info('Shop imported successfully.');
      return redirect()->back()->with('success', 'Shops imported successfully.');
    } catch (ValidationException $e) {
      Log::error('Validation error: ' . $e->getMessage());
      return redirect()->back()->withErrors($e->validator);
    } catch (\Exception $e) {
      Log::error('Error importing shops: ' . $e->getMessage());
      return back()->withError('An error occurred while importing shops. Please try again.');
    }
  }

  public function validateRequest(Request $request)
  {
    $validatedData = $request->validate([
      'file' => 'required|mimes:xlsx,xls,csv|max:51200',
    ], [
      'file.required' => 'ファイルを選択してください。',
      'file.mimes' => '有効なファイルタイプは xlsx, xls, csv のみです。',
      'file.max' => 'ファイルサイズは最大 50MB までです。',
    ]);

    return $validatedData;
  }

  // public function import(Request $request)
  // {
  //   try {
  //     // 一時的にコメントアウト⇩
  //     // $this->validateRequest($request);

  //     $file = $request->file('file');
  //     $extension = $file->getClientOriginalExtension();

  //     // CSVファイルの内容をログに出力
  //     $content = file_get_contents($file->getRealPath());
  //     Log::info('CSV file content:', [$content]);

  //     switch ($extension) {
  //       case 'xlsx':
  //         $readerType = \Maatwebsite\Excel\Excel::XLSX;
  //         break;
  //       case 'xls':
  //         $readerType = \Maatwebsite\Excel\Excel::XLS;
  //         break;
  //       case 'csv':
  //         $readerType = \Maatwebsite\Excel\Excel::CSV;
  //         break;
  //       default:
  //         throw new \InvalidArgumentException("Unsupported file type: {$extension}");
  //     }

  //     $import = new ShopImport();
  //     Excel::import($import, $file, null, $readerType);

  //     if (session()->has('import_errors')) {
  //       return redirect()->back()->withErrors(session()->get('import_errors'));
  //     }

  //     Log::info('Shop imported successfully.');
  //     return redirect()->back()->with('success', 'Shops imported successfully.');
  //   } catch (ValidationException $e) {
  //     return redirect()->back()->withErrors($e->validator);
  //   } catch (\Exception $e) {
  //     Log::error('Error importing shops: ' . $e->getMessage());
  //     return back()->withError('An error occurred while importing shops. Please try again.');
  //   }
  // }

  // public function validateRequest(Request $request)
  // {
  //   $validatedData = $request->validate([
  //     'file' => 'required|mimes:xlsx,xls,csv|max:51200',
  //   ], [
  //     'file.required' => 'ファイルを選択してください。',
  //     'file.mimes' => '有効なファイルタイプは xlsx, xls, csv のみです。',
  //     'file.max' => 'ファイルサイズは最大 50MB までです。',
  //   ]);

  //   return $validatedData;
  // }

  public function importForm()
  {
    return view('admin.shops.import');
  }
}
