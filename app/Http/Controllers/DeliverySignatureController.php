<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeliverySignatureController extends Controller
{
    public function show(Delivery $delivery)
    {
        // Allow both the customer and admin to access the signature page
        $user = Auth::user();
        $isCustomer = $delivery->order && $delivery->order->user_id === $user->id;
        $isAdmin = $user->isAdmin(); // Assuming you have an isAdmin method or similar check
        
        if (!$isCustomer && !$isAdmin) {
            abort(403, 'You are not authorized to sign for this delivery.');
        }

        return view('deliveries.signature', compact('delivery'));
    }

    public function store(Request $request, Delivery $delivery)
    {
        // Validate the request
        $request->validate([
            'signature' => 'required|string',
            'conditions' => 'required|array',
            'conditions.*' => 'required|string',
        ]);

        // Allow both the customer and admin to submit the signature
        $user = Auth::user();
        $isCustomer = $delivery->order && $delivery->order->user_id === $user->id;
        $isAdmin = $user->isAdmin();
        
        if (!$isCustomer && !$isAdmin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Additional check to ensure delivery is in transit
        if (!$delivery->isInTransit()) {
            return response()->json(['error' => 'This delivery is not ready for signature.'], 400);
        }

        try {
            // Process the signature image
            $signatureData = $request->input('signature');
            $signaturePath = null;
            
            // Extract the base64 image data
            if (preg_match('/^data:image\/(\w+);base64,/', $signatureData, $type)) {
                $data = substr($signatureData, strpos($signatureData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                    throw new \Exception('Invalid image type. Only JPG, JPEG, and PNG are supported.');
                }
                
                $data = base64_decode($data);
                if ($data === false) {
                    throw new \Exception('Base64 decode failed');
                }
                
                // Create directory if it doesn't exist
                $directory = 'public/signatures';
                if (!file_exists(storage_path('app/' . $directory))) {
                    mkdir(storage_path('app/' . $directory), 0755, true);
                }
                
                // Generate unique filename
                $filename = 'signature_' . $delivery->id . '_' . time() . '.png';
                $relativePath = 'signatures/' . $filename;
                $fullPath = storage_path('app/public/' . $relativePath);
                
                // Ensure the directory exists
                if (!file_exists(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }
                
                // Save the file
                if (file_put_contents($fullPath, $data) === false) {
                    throw new \Exception('Failed to save signature file');
                }
                
                // Store the relative path (without 'storage/' prefix)
                $signaturePath = $relativePath;
            } else {
                throw new \Exception('Invalid signature data format');
            }

            // Update the delivery with signature path, conditions, and mark as delivered
            $delivery->update([
                'customer_signature' => $signaturePath,
                'signature_ip_address' => $request->ip(),
                'signed_at' => now(),
                'delivery_conditions' => $request->input('conditions'),
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            // Log the signature
            Log::info("Delivery #{$delivery->id} signed by customer", [
                'delivery_id' => $delivery->id,
                'signed_at' => now(),
                'ip' => $request->ip(),
                'signature_path' => $signaturePath,
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.deliveries.show', $delivery)
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving delivery signature: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save signature: ' . $e->getMessage()], 500);
        }
    }
}
