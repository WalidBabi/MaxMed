<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;
use App\Models\SystemFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        SystemFeedback::create([
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'pending',
        ]);

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