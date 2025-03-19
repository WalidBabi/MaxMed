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

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
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
            $order = Order::findOrFail($request->order_id);
            
            // Confirm reservations and update inventory
            foreach ($order->items as $item) {
                // Update reservation status
                ProductReservation::where([
                    'product_id' => $item->product_id,
                    'session_id' => session()->getId(),
                    'status' => 'pending'
                ])->update(['status' => 'confirmed']);

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
                'user_id' => auth()->id(),
                'transaction_id' => $request->session_id,     
                'payment_method' => 'stripe',
                'amount' => $order->total_amount,
                'status' => 'completed'
            ]);

            // Send email notifications with explicit sender
            $recipients = ['walid.babi.du@gmail.com', 'mohanad.babi@gmail.com'];
            foreach ($recipients as $email) {
                Mail::to($email)
                    ->from('cs@maxmedme.com', 'MaxMedMe Customer Service')
                    ->send(new OrderPlaced($order));
            }

            session()->forget('cart');
            
            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            return redirect()->route('cart.view')
                ->with('error', 'Something went wrong with the payment.');
        }
    }

    private function calculateTotal($cart)
    {
        return collect($cart)->sum(function($item) {
            return $item['price'] * $item['quantity'];
        });
    }
}