<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;


class QrCodeService
{
  public function generate(string $data): string
  {
    $qrCode = new QrCode($data);
    $qrCode->setSize(300); // サイズを設定
    $qrCode->setMargin(10); // マージンを設定

    $writer = new PngWriter();

    // QRコードを生成し、データURI形式で返す
    return $writer->write($qrCode)->getDataUri();
  }
}
