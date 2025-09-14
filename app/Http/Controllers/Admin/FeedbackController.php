<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\SystemFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:feedback.view')->only(['index', 'show']);
        $this->middleware('permission:feedback.respond')->only(['respond', 'storeResponse']);
        $this->middleware('permission:feedback.delete')->only(['destroy']);
        $this->middleware('permission:feedback.export')->only(['export']);
    }

    /**
     * Display a listing of feedbacks.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'order'); // 'order' or 'system'
        
        // Get order feedback with filters
        $orderFeedbackQuery = Feedback::with(['user', 'order'])
            ->when($request->filled('rating'), function ($query) use ($request) {
                $query->where('rating', $request->get('rating'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('feedback', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('order', function ($orderQuery) use ($search) {
                          $orderQuery->where('order_number', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->get('type') === 'order', function ($query) {
                // Additional filter for order feedback if needed
            })
            ->orderBy('created_at', 'desc');

        // Get system feedback with filters
        $systemFeedbackQuery = SystemFeedback::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->get('status'));
            })
            ->when($request->filled('priority'), function ($query) use ($request) {
                $query->where('priority', $request->get('priority'));
            })
            ->when($request->filled('feedback_type'), function ($query) use ($request) {
                $query->where('type', $request->get('feedback_type'));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($request->get('type') === 'system', function ($query) {
                // Additional filter for system feedback if needed
            })
            ->orderBy('created_at', 'desc');

        // Apply date filter if provided
        if ($request->filled('date_from')) {
            $orderFeedbackQuery->whereDate('created_at', '>=', $request->get('date_from'));
            $systemFeedbackQuery->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $orderFeedbackQuery->whereDate('created_at', '<=', $request->get('date_to'));
            $systemFeedbackQuery->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Paginate the results
        $orderFeedback = $orderFeedbackQuery->paginate(15, ['*'], 'order_page');
        $systemFeedback = $systemFeedbackQuery->paginate(15, ['*'], 'system_page');

        // Calculate statistics for the dashboard cards
        $totalFeedback = Feedback::count() + SystemFeedback::count();
        $pendingReviews = SystemFeedback::where('status', 'pending')->count();
        $averageRating = Feedback::avg('rating') ?: 0;
        $systemReports = SystemFeedback::count();
        
        // Count for tabs
        $orderFeedbackCount = Feedback::count();
        $systemFeedbackCount = SystemFeedback::count();

        return view('admin.feedback.index', compact(
            'orderFeedback',
            'systemFeedback',
            'totalFeedback',
            'pendingReviews',
            'averageRating',
            'systemReports',
            'orderFeedbackCount',
            'systemFeedbackCount',
            'tab'
        ));
    }

    /**
     * Display the specified feedback.
     */
    public function show(Request $request, $id)
    {
        $feedback = Feedback::with(['user', 'order'])->findOrFail($id);
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Display the specified system feedback.
     */
    public function showSystem(SystemFeedback $systemFeedback)
    {
        return view('admin.feedback.show-system', compact('systemFeedback'));
    }

    /**
     * Update system feedback status and response.
     */
    public function update(Request $request, SystemFeedback $systemFeedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $systemFeedback->update($validated);

        return redirect()->back()->with('success', 'System feedback updated successfully!');
    }

    /**
     * Update system feedback status and response.
     */
    public function updateSystem(Request $request, SystemFeedback $systemFeedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,rejected',
            'admin_response' => 'nullable|string|max:2000',
        ]);

        $systemFeedback->update($validated);

        return redirect()->back()->with('success', 'System feedback updated successfully!');
    }

    /**
     * Get feedback statistics for dashboard.
     */
    public function stats()
    {
        $orderFeedbackStats = [
            'total' => Feedback::count(),
            'average_rating' => round(Feedback::avg('rating'), 1),
            'recent' => Feedback::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $systemFeedbackStats = [
            'total' => SystemFeedback::count(),
            'pending' => SystemFeedback::where('status', 'pending')->count(),
            'high_priority' => SystemFeedback::where('priority', 'high')->count(),
            'recent' => SystemFeedback::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return response()->json([
            'order_feedback' => $orderFeedbackStats,
            'system_feedback' => $systemFeedbackStats,
        ]);
    }
} 