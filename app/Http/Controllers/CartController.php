<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReservation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getTotalQuantityInCart($productId)
    {
        $cart = session()->get('cart', []);
        return isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
    }

    /**
     * Display the user's cart.
     */
    public function viewCart(Request $request)
    {
        if (!session()->has('cart')) {
            session(['cart' => []]);
        }
        $cart = session('cart', []);
        return view('cart', compact('cart'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request, Product $product)
    {
        // If it's a GET request, default to quantity=1
        $requestedQuantity = $request->isMethod('get') ? 1 : $request->input('quantity', 1);
        \Log::info('Requested Quantity:', ['quantity' => $requestedQuantity]);
        
        // Get current cart quantity for this product
        $currentCartQuantity = $this->getTotalQuantityInCart($product->id);
        $totalRequestedQuantity = $currentCartQuantity + $requestedQuantity;
        
        // Check available quantity (considering existing reservations)
        $availableQuantity = $this->getAvailableQuantity($product);
        
        if ($availableQuantity <= 0) {
            return redirect()->back()->with('error', 'This product is no longer available in stock.');
        }

        if ($totalRequestedQuantity > $availableQuantity) {
            return redirect()->back()->with('error', 'The requested quantity exceeds the available stock.');
        }

        // Update or create reservation
        ProductReservation::updateOrCreate(
            [
                'session_id' => session()->getId(),
                'product_id' => $product->id,
                'status' => 'pending'
            ],
            [
                'quantity' => $totalRequestedQuantity,
                'user_id' => Auth::check() ? Auth::id() : null,
                'expires_at' => now()->addMinutes(1)
            ]
        );

        $cart = session()->get('cart', []);

        // Update cart
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] = $totalRequestedQuantity;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => $requestedQuantity,
                'price' => $product->price,
                'photo' => $product->image_url
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            // Remove the reservation
            ProductReservation::where([
                'session_id' => session()->getId(),
                'product_id' => $product->id,
                'status' => 'pending'
            ])->delete();

            // Remove from cart
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.view')->with('success', 'Product removed from cart successfully!');
    }

    private function getAvailableQuantity(Product $product)
    {
        // Get all valid reservations except the current user's
        $otherReservations = ProductReservation::where('product_id', $product->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->where('session_id', '!=', session()->getId())
            ->sum('quantity');

        return $product->inventory->quantity - $otherReservations;
    }

    /**
     * Update the quantity of an item in the cart
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        
        if(isset($cart[$id])) {
            // Increase quantity
            if($request->has('increase')) {
                $cart[$id]['quantity']++;
            }
            
            // Decrease quantity, remove if it would be 0
            if($request->has('decrease')) {
                if($cart[$id]['quantity'] > 1) {
                    $cart[$id]['quantity']--;
                } else {
                    unset($cart[$id]);
                }
            }
            
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Cart updated successfully!');
        }
        
        return redirect()->back()->with('error', 'Product not found in cart!');
    }
} 