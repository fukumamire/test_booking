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
    $this->middleware('auth:admin');
  }

  public function import(Request $request)
  {
    try {
      $this->validateRequest($request);

      $filePath = $request->file('file')->getPathname();
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
          throw new \InvalidArgumentException("Unsupported file type: {$extension}");
      }

      $import = new ShopImport();
      Excel::import($import, $request->file('file'), null, $readerType);

      Log::info('Shop imported successfully.');

      return redirect()->back()->with('success', 'Shops imported successfully.');
    } catch (\Exception $e) {
      Log::error('Error importing shops: ' . $e->getMessage());
      return back()->withError('An error occurred while importing shops. Please try again.');
    }
  }
  public function importForm()
  {
    return view('admin.shops.import');
  }
}
