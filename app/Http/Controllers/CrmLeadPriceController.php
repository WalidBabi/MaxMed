<?php

namespace App\Http\Controllers;

use App\Models\CrmLead;
use App\Models\LeadPriceSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CrmLeadPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        // Only users with purchasing permissions can submit prices
        $this->middleware(function ($request, $next) {
            if (!$this->hasPurchasingPermissions(Auth::user())) {
                abort(403, 'You do not have permission to submit prices.');
            }
            return $next($request);
        });
    }

    /**
     * Check if user has purchasing permissions
     */
    private function hasPurchasingPermissions($user)
    {
        $purchasingPermissions = [
            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.edit',
            'quotations.create',
            'quotations.view',
            'crm.leads.view_requirements'
        ];
        
        foreach ($purchasingPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user has purchasing specialist role (restricted to assigned leads only)
     */
    private function isPurchasingSpecialist($user)
    {
        // Check if user has a role named "purchasing-specialist" or similar restricted roles
        if (method_exists($user, 'role') && $user->role) {
            $roleName = strtolower($user->role->name ?? '');
            $restrictedRoles = [
                'purchasing-specialist',
                'purchasing_assistant', 
                'crm-assistant',
                'purchasing_crm_assistant',
                'supplier'
            ];
            
            foreach ($restrictedRoles as $restrictedRole) {
                if (strpos($roleName, $restrictedRole) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Store a new price submission
     */
    public function store(Request $request, CrmLead $lead)
    {
        // Check if the lead is assigned to the current user (for purchasing specialists)
        $user = Auth::user();
        
        // If user has purchasing specialist role, they can only submit prices for leads assigned to them
        if ($this->isPurchasingSpecialist($user)) {
            if ($lead->assigned_to !== $user->id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only submit prices for leads assigned to you.'
                    ], 403);
                }
                
                return redirect()->back()->withErrors(['error' => 'You can only submit prices for leads assigned to you.']);
            }
        }
        
        $validated = $request->validate([
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:AED,USD,EUR',
            'notes' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp,xls,xlsx|max:10240', // Max 10MB per file
        ]);

        try {
            $priceSubmission = new LeadPriceSubmission();
            $priceSubmission->crm_lead_id = $lead->id;
            $priceSubmission->user_id = Auth::id();
            $priceSubmission->price = $validated['price'];
            $priceSubmission->currency = $validated['currency'];
            $priceSubmission->notes = $validated['notes'];
            $priceSubmission->status = 'submitted';
            $priceSubmission->submitted_at = now();

            // Handle file uploads
            if ($request->hasFile('attachments')) {
                $attachments = [];
                foreach ($request->file('attachments') as $file) {
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . uniqid() . '_' . $originalName;
                        $filePath = $file->storeAs('price_submissions', $fileName, 'public');
                        
                        $attachments[] = [
                            'path' => $filePath,
                            'original_name' => $originalName,
                            'size' => $file->getSize(),
                            'mime_type' => $file->getClientMimeType(),
                            'uploaded_at' => now()->toISOString(),
                        ];
                    }
                }
                $priceSubmission->attachments = $attachments;
            }

            $priceSubmission->save();

            // Log activity on the lead
            $lead->logActivity('price_submitted', 'Price submitted', 
                "Price of {$priceSubmission->formatted_price} submitted by " . Auth::user()->name);

            // Update lead status to price_submitted if it's not already at a later stage
            if (in_array($lead->status, ['new_inquiry', 'quote_requested', 'getting_price'])) {
                $lead->status = 'price_submitted';
                $lead->save();
                $lead->logActivity('status_change', 'Status updated', 
                    "Status automatically updated to 'price_submitted' after price submission");
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price submitted successfully!',
                    'price_submission' => $priceSubmission->load('user')
                ]);
            }

            return redirect()->back()->with('success', 'Price submitted successfully!');

        } catch (\Exception $e) {
            Log::error('Price submission failed', [
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit price. Please try again.'
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to submit price. Please try again.']);
        }
    }

    /**
     * Get price submissions for a lead
     */
    public function index(CrmLead $lead)
    {
        $priceSubmissions = $lead->priceSubmissions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'price_submissions' => $priceSubmissions
            ]);
        }

        return view('crm.leads.price-submissions', compact('lead', 'priceSubmissions'));
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(LeadPriceSubmission $priceSubmission, $attachmentIndex)
    {
        if (!$priceSubmission->attachments || !isset($priceSubmission->attachments[$attachmentIndex])) {
            abort(404, 'Attachment not found');
        }

        $attachment = $priceSubmission->attachments[$attachmentIndex];
        $filePath = storage_path('app/public/' . $attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->download($filePath, $attachment['original_name']);
    }
}
