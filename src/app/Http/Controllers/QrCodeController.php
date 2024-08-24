<?php

namespace App\Http\Controllers;

use App\Models\Booking; 
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
  
  protected $qrCodeService;

  public function __construct(QrCodeService $qrCodeService)
  {
    $this->qrCodeService = $qrCodeService;
  }

  // 予約時にQRコードを生成するメソッド

  public function generateQrCode($bookingId)
  {
    $booking = Booking::findOrFail($bookingId); // 予約IDで予約情報を取得
    $token = base64_encode(random_bytes(16)); // 仮のトークンを生成
    $booking->update(['qr_code_token' => $token]); // 予約情報にトークンを保存

    $qrCodeUrl = route('reservation.scan', ['token' => $token]); // スキャン時にアクセスするURLを生成
    $qrCodeDataUri = $this->qrCodeService->generate($qrCodeUrl); // 依存注入されたQrCodeServiceを使用してQRコードを生成

    return view('auth.qr-code', ['qrCode' => $qrCodeDataUri]); // QRコードを表示するビューに渡す
  }
  // 店舗側　QRコードのスキャンと認証メソッド
  public function authenticateReservation(Request $request)
  {
    $token = $request->query('token'); // URLからトークンを取得
    $booking = Booking::where('qr_code_token', $token)->first(); // トークンで予約を検索

    if ($booking) {
      // 予約が見つかった場合、認証成功の処理を実行
      return redirect()->route('reservation.success');
    } else {
      // 予約が見つからない場合、エラーを返す
      return redirect()->route('reservation.failure');
    }
  }
}
