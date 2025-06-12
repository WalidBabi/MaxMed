<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string|max:1000',
        ]);

        // Check if the order belongs to the authenticated user
        $order = Order::where('id', $validated['order_id'])
                     ->where('user_id', Auth::id())
                     ->firstOrFail();

        $feedback = new Feedback();
        $feedback->user_id = Auth::id();
        $feedback->order_id = $validated['order_id'];
        $feedback->rating = $validated['rating'];
        $feedback->feedback = $validated['feedback'];
        $feedback->save();

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
} 