<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function saveSignature(Request $request, $deliveryId)
    {
        $delivery = Delivery::findOrFail($deliveryId);
        
        if (!$delivery->customer_signature) {
            return response()->json(['error' => 'No signature found for this delivery'], 404);
        }
        
        try {
            // Extract the base64 data
            $base64_image = $delivery->customer_signature;
            $image_parts = explode("base64,", $base64_image);
            
            if (count($image_parts) !== 2) {
                throw new \Exception('Invalid base64 string format');
            }
            
            $image_base64 = base64_decode($image_parts[1]);
            
            if ($image_base64 === false) {
                throw new \Exception('Failed to decode base64 string');
            }
            
            // Create signatures directory if it doesn't exist
            $directory = storage_path('app/public/signatures');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Save the file
            $filename = 'signature_' . $delivery->id . '_' . time() . '.png';
            $filepath = $directory . '/' . $filename;
            
            if (file_put_contents($filepath, $image_base64) === false) {
                throw new \Exception('Failed to save signature file');
            }
            
            // Update delivery record with the file path if needed
            // $delivery->signature_path = 'signatures/' . $filename;
            // $delivery->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Signature saved successfully',
                'path' => 'storage/signatures/' . $filename
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to save signature: ' . $e->getMessage()
            ], 500);
        }
    }
}
