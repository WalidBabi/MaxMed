<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeController extends Controller
{
    public function generate()
    {
        // Replace 'https://maxmedme.com' with your actual production domain
        $url = 'https://maxmedme.com';
        
        return response(
            QrCode::size(300)
                  ->backgroundColor(255, 255, 255)
                  ->color(23, 30, 96) // Using your brand color from the footer (#171e60)
                  ->generate($url)
        )->header('Content-Type', 'image/svg+xml');
    }

    public function saveQRCode()
    {
        $url = 'https://maxmedme.com'; // Replace with your actual domain
        $path = public_path('images/website-qr.png');
        
        QrCode::size(300)
              ->backgroundColor(255, 255, 255)
              ->color(0, 0, 0) // Using black color for professional appearance
              ->format('png')
              ->generate($url, $path);
        
        return 'QR Code saved successfully!';
    }
} 