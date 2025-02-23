<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
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
    public function addToCart(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                "name" => $product->name,
                "quantity" => $request->input('quantity'),
                "price" => $product->price,
                "photo" => $product->image_url
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.view')->with('success', 'Product added to cart successfully!');
    }

    /**
     * Remove a product from the cart.
     */
    public function removeFromCart(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$product->id])) {
            unset($cart[$product->id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.view')->with('success', 'Product removed from cart successfully!');
    }
} 