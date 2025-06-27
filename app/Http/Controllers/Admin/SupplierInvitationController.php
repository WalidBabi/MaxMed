<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierInvitation;
use App\Mail\SupplierInvitationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SupplierInvitationController extends Controller
{
    /**
     * Display a listing of supplier invitations.
     */
    public function index(Request $request)
    {
        $query = SupplierInvitation::with(['inviter', 'user']);
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Search by email or name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }
        
        $invitations = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return View::make('admin.supplier-invitations.index', compact('invitations'));
    }

    /**
     * Show the form for creating a new supplier invitation.
     */
    public function create()
    {
        return View::make('admin.supplier-invitations.create');
    }

    /**
     * Store a newly created supplier invitation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email|unique:supplier_invitations,email,NULL,id,status,pending',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            // Create invitation
            $invitation = SupplierInvitation::create([
                'email' => $request->email,
                'name' => $request->name,
                'company_name' => $request->company_name,
                'invited_by' => Auth::id(),
                'custom_message' => $request->custom_message,
                'token' => Str::random(60),
                'status' => 'pending',
                'expires_at' => now()->addDays(7),
            ]);

            // Send invitation email
            Mail::to($invitation->email)->send(new SupplierInvitationMail(
                $invitation->email,
                $invitation->name,
                $invitation->token,
                $invitation->company_name ?? '',
                Auth::user()->name ?? 'MaxMed Admin',
                $invitation->custom_message ?? ''
            ));

            DB::commit();

            return redirect()->route('admin.supplier-invitations.index')
                ->with('success', 'Supplier invitation sent successfully! The supplier will receive an email with onboarding instructions.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send supplier invitation: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Failed to send invitation. Please try again.');
        }
    }

    /**
     * Display the specified supplier invitation.
     */
    public function show(SupplierInvitation $supplierInvitation)
    {
        $supplierInvitation->load(['inviter', 'user']);
        return View::make('admin.supplier-invitations.show', ['invitation' => $supplierInvitation]);
    }

    /**
     * Resend a supplier invitation.
     */
    public function resend(SupplierInvitation $supplierInvitation)
    {
        if ($supplierInvitation->status !== SupplierInvitation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending invitations can be resent.');
        }

        if ($supplierInvitation->isExpired()) {
            // Extend the expiration date
            $supplierInvitation->update([
                'expires_at' => now()->addDays(7),
            ]);
        }

        try {
            // Resend the invitation email
            Mail::to($supplierInvitation->email)->send(new SupplierInvitationMail(
                $supplierInvitation->email,
                $supplierInvitation->name,
                $supplierInvitation->token,
                $supplierInvitation->company_name,
                auth()->user()->name,
                $supplierInvitation->custom_message
            ));

            return redirect()->back()->with('success', 'Invitation resent successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to resend supplier invitation: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to resend invitation. Please try again.');
        }
    }

    /**
     * Cancel a supplier invitation.
     */
    public function cancel(SupplierInvitation $supplierInvitation)
    {
        if ($supplierInvitation->status !== SupplierInvitation::STATUS_PENDING) {
            return redirect()->back()->with('error', 'Only pending invitations can be cancelled.');
        }

        $supplierInvitation->update(['status' => SupplierInvitation::STATUS_CANCELLED]);

        return redirect()->back()->with('success', 'Invitation cancelled successfully.');
    }

    /**
     * Remove the specified supplier invitation.
     */
    public function destroy(SupplierInvitation $supplierInvitation)
    {
        $supplierInvitation->delete();

        return redirect()->route('admin.supplier-invitations.index')
            ->with('success', 'Invitation deleted successfully.');
    }

    /**
     * Show the onboarding flow preview for admins
     */
    public function onboardingPreview()
    {
        $categories = \App\Models\Category::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
            
        return View::make('admin.supplier-invitations.onboarding-preview', compact('categories'));
    }

    /**
     * Bulk actions for supplier invitations.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:resend,cancel,delete',
            'invitation_ids' => 'required|array|min:1',
            'invitation_ids.*' => 'exists:supplier_invitations,id',
        ]);

        $invitations = SupplierInvitation::whereIn('id', $validated['invitation_ids'])->get();
        $successCount = 0;

        DB::beginTransaction();
        try {
            foreach ($invitations as $invitation) {
                switch ($validated['action']) {
                    case 'resend':
                        if ($invitation->status === SupplierInvitation::STATUS_PENDING) {
                            if ($invitation->isExpired()) {
                                $invitation->update(['expires_at' => now()->addDays(7)]);
                            }
                            
                            Mail::to($invitation->email)->send(new SupplierInvitationMail(
                                $invitation->email,
                                $invitation->name,
                                $invitation->token,
                                $invitation->company_name,
                                auth()->user()->name,
                                $invitation->custom_message
                            ));
                            $successCount++;
                        }
                        break;
                        
                    case 'cancel':
                        if ($invitation->status === SupplierInvitation::STATUS_PENDING) {
                            $invitation->update(['status' => SupplierInvitation::STATUS_CANCELLED]);
                            $successCount++;
                        }
                        break;
                        
                    case 'delete':
                        $invitation->delete();
                        $successCount++;
                        break;
                }
            }

            DB::commit();

            $actionName = ucfirst($validated['action']);
            return redirect()->back()->with('success', 
                "{$actionName} action completed successfully for {$successCount} invitation(s).");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Failed bulk action on supplier invitations: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Bulk action failed. Please try again.');
        }
    }
} 