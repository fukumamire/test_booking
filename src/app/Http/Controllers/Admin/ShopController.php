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
      Excel::import($import, $file->getRealPath(), null, \Maatwebsite\Excel\Excel::CSV);

      Log::info('Shop imported successfully.');
      return redirect()->back()->with('success', 'Shops imported successfully.');
    } catch (\Exception $e) {
      Log::error('Error importing shops: ' . $e->getMessage());
      return redirect()->back()->withErrors(['error' => 'An error occurred while importing shops. Please try again.']);
    }
  }

  public function importForm()
  {
    return view('admin.shops.import');
  }
}
