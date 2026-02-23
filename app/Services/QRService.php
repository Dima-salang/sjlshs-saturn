<?php

namespace App\Services;

use App\Models\QRCode as QRCodeModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// QR service for generating qr codes

class QRService
{
    public function generateQRCode(array $data): QRCodeModel
    {
        // get the student data from the param
        $lrn = $data['lrn'];
        $last_name = $data['last_name'];
        $section = $data['section'];

        $qrCode = QrCode::size(256)->format('png')->generate(json_encode($data));
        $path = "{$lrn} - {$last_name}.png";

        $fullpath = $this->saveQRToStorage($qrCode, $path, $section);

        return QRCodeModel::create([
            'data' => $data,
            'path' => $fullpath,
        ]);
    }

    /**
     * Save QR code to storage
     *
     * @return string $fullpath
     */
    private function saveQRToStorage(string $qrCode, string $path, string $section): string
    {
        $folderpath = storage_path("app/public/qrcodes/{$section}");

        if (! file_exists($folderpath)) {
            mkdir($folderpath, 0755, true);
        }
        file_put_contents($folderpath.'/'.$path, $qrCode);

        return $folderpath.'/'.$path;
    }
}
