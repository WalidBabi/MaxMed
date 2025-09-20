<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Customer;
use App\Mail\DeliveryNoteEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            // Send delivery note email automatically after signature
            $this->sendDeliveryNoteEmail($delivery);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.deliveries.show', $delivery)
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving delivery signature: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to save signature: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Send delivery note email to customer after signature
     */
    private function sendDeliveryNoteEmail(Delivery $delivery)
    {
        try {
            // Get customer email
            $customerEmail = null;
            
            // First try to get email from the order's customer
            if ($delivery->order && $delivery->order->user && $delivery->order->user->email) {
                $customerEmail = $delivery->order->user->email;
            } elseif ($delivery->order && $delivery->order->customer_name) {
                // Try to find customer by name
                $customer = Customer::where('name', $delivery->order->customer_name)->first();
                if ($customer && $customer->email) {
                    $customerEmail = $customer->email;
                }
            }

            if (!$customerEmail) {
                Log::warning("No email found for delivery {$delivery->delivery_number}, skipping delivery note email");
                return;
            }

            // Prepare email data
            $emailData = [
                'subject' => 'Delivery Confirmation - ' . $delivery->delivery_number,
                'message' => 'Thank you for receiving your order. Please find the delivery note attached for your records.'
            ];

            // Send the email
            $salesEmail = app()->environment('production') ? 'sales@maxmedme.com' : 'wbabi@localhost.com';
            
            Mail::to($customerEmail)
                ->cc($salesEmail)
                ->send(new DeliveryNoteEmail($delivery, $emailData));

            Log::info("Delivery note email sent successfully for delivery {$delivery->delivery_number} to {$customerEmail}");

        } catch (\Exception $e) {
            Log::error("Failed to send delivery note email for delivery {$delivery->delivery_number}: " . $e->getMessage());
            // Don't throw the exception to avoid disrupting the signature process
        }
    }
}
