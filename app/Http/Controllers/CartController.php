<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductReservation;

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
        $requestedQuantity = $request->input('quantity');
        
        // Get current cart quantity for this product
        $currentCartQuantity = $this->getTotalQuantityInCart($product->id);
        $totalRequestedQuantity = $currentCartQuantity + $requestedQuantity;
        
        // Check available quantity (considering existing reservations)
        $availableQuantity = $this->getAvailableQuantity($product);
        
        if ($totalRequestedQuantity > $availableQuantity) {
            return redirect()->back()->with('error', 'Requested quantity exceeds available stock!');
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
                'user_id' => auth()->id(),
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
} 