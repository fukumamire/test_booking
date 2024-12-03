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
      $file = $request->file('file');
      Log::info('File uploaded: ' . $file->getClientOriginalName());

      $import = new ShopImport();
      Excel::import($import, $file, null, \Maatwebsite\Excel\Excel::CSV, function ($reader) {
        $reader->noHeading();
      });

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
      'file' => 'required|mimes:csv,txt|max:51200',
    ], [
      'file.required' => 'ファイルを選択してください。',
      'file.mimes' => '有効なファイルタイプは csv, txt のみです。',
      'file.max' => 'ファイルサイズは最大 50MB までです。',
    ]);

    Log::info('Validated data:', $validatedData);

    return $validatedData;
  }

  public function importForm()
  {
    return view('admin.shops.import');
  }
}
