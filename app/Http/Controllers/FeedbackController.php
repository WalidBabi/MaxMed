<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FeedbackNotification;
use Illuminate\Support\Facades\Log;

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

        // Send notification to all admin users about new feedback
        try {
            // Get all admin users with admin role
            $admins = User::whereHas('role', function($roleQuery) {
                $roleQuery->where('name', 'admin');
            })
            ->whereNotNull('email')
            ->get();

            if ($admins->count() > 0) {
                Notification::send($admins, new FeedbackNotification($feedback));
                Log::info('Feedback notification sent to ' . $admins->count() . ' admin(s) for feedback ID: ' . $feedback->id);
            } else {
                Log::warning('No admin users found to send feedback notification for feedback ID: ' . $feedback->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send feedback notification: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Thank you for your feedback!');
    }
} 