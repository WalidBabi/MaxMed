<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\ProductReservation;
use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'You need to be logged in to proceed.');
        }

        \Log::info('User ID: ' . auth()->id());

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create order first
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'total_amount' => $this->calculateTotal(session('cart')),
            'status' => 'pending',
            'shipping_address' => $request->address ?? 'Default Address', // You should collect this in a previous step
            'shipping_city' => $request->city ?? 'Default City',
            'shipping_state' => $request->state ?? 'Default State',
            'shipping_zipcode' => $request->zipcode ?? '12345',
            'shipping_phone' => $request->phone ?? '1234567890',
        ]);

        // Create order items
        foreach(session('cart') as $id => $details) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price']
            ]);
        }

        $lineItems = [];
        foreach(session('cart') as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item['name'],
                        'images' => [rawurlencode(asset($item['photo']))],
                    ],
                    'unit_amount' => $item['price'] * 100,
                ],
                'quantity' => $item['quantity'],
            ];
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order->id,
            'cancel_url' => route('cart.view'),
            'metadata' => [
                'order_id' => $order->id
            ]
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        try {
            \Log::info('Stripe success callback received', ['order_id' => $request->order_id, 'session_id' => $request->session_id]);
            
            $order = Order::findOrFail($request->order_id);
            
            // Confirm reservations and update inventory
            foreach ($order->items as $item) {
                // Update reservation status
                ProductReservation::where([
                    'product_id' => $item->product_id,
                    'status' => 'pending'
                ])
                ->where(function($query) use ($order) {
                    $query->where('user_id', $order->user_id);
                })
                ->update([
                    'status' => 'confirmed'
                ]);

                // Decrease inventory
                $inventory = $item->product->inventory;
                $inventory->quantity -= $item->quantity;
                $inventory->save();
            }

            $order->status = 'processing';
            $order->save();

            // Save transaction
            Transaction::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'transaction_id' => $request->session_id,     
                'payment_method' => 'stripe',
                'amount' => $order->total_amount,
                'status' => 'completed'
            ]);

            // Try to send email notification, but don't fail if it doesn't work
            try {
                Mail::to('cs@maxmedme.com')
                    ->send(new OrderPlaced($order));
            } catch (\Exception $mailException) {
                Log::warning('Failed to send order confirmation email: ' . $mailException->getMessage());
                // Continue processing - don't let email failure affect the order process
            }

            // Clear the cart session if user is still the same
            if (auth()->id() && auth()->id() == $order->user_id) {
                session()->forget('cart');
            }
            
            // Redirect to My Orders page
            return redirect()->route('orders.index')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            Log::error('Payment success error: ' . $e->getMessage());
            return redirect()->route('cart.view')
                ->with('error', 'Something went wrong with the payment: ' . $e->getMessage());
        }
    }

    private function calculateTotal($cart)
    {
        return collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
    }
}