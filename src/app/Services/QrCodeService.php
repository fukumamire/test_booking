<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QrCodeService
{
  public function generate(string $data): string
  {
    // QRコードを生成し、データURI形式で返す
    $qrCodeBinaryData = QrCode::format('png')->size(300)->generate($data);
    return 'data:image/png;base64,' . base64_encode($qrCodeBinaryData);
    
  }
}
