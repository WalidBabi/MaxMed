<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SystemFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SystemFeedbackController extends Controller
{
    public function index()
    {
        $feedback = SystemFeedback::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('supplier.feedback.index', compact('feedback'));
    }

    public function create()
    {
        return view('supplier.feedback.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:bug_report,feature_request,improvement,general',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority' => 'required|in:low,medium,high',
        ]);

        $systemFeedback = SystemFeedback::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

        // Send notification to all admin users
        try {
            // Get all admin users using the role relationship
            $admins = \App\Models\User::whereHas('role', function($query) {
                $query->where('name', 'admin');
            })
            ->whereNotNull('email')
            ->get();

            if ($admins->count() > 0) {
                // Send styled email to each admin
                foreach ($admins as $admin) {
                    \Illuminate\Support\Facades\Mail::to($admin->email)->send(new \App\Mail\AdminSystemFeedbackSubmitted($systemFeedback));
                }
                
                \Illuminate\Support\Facades\Log::info('System feedback styled email sent to ' . $admins->count() . ' admin(s) for feedback ID: ' . $systemFeedback->id);
            } else {
                \Illuminate\Support\Facades\Log::warning('No admin users found to send system feedback notification for feedback ID: ' . $systemFeedback->id);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send system feedback notification: ' . $e->getMessage());
        }

        return redirect()->route('supplier.feedback.index')
                        ->with('success', 'Thank you for your feedback! We will review it and get back to you.');
    }

    public function show(SystemFeedback $feedback)
    {
        // Ensure the feedback belongs to the current user
        if ($feedback->user_id !== Auth::id()) {
            abort(403);
        }

        return view('supplier.feedback.show', compact('feedback'));
    }
} 