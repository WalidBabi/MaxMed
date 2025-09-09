<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CrmLeadOwnershipMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Ensures users can only edit their own CRM leads unless they have admin permissions
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Allow admin roles to bypass this restriction
        if ($user->hasAnyRole(['super_admin', 'admin', 'business_admin', 'crm_manager', 'crm-administrator'])) {
            return $next($request);
        }
        
        // For lead-related routes, check ownership
        $leadId = $request->route('lead') ?? $request->route('id') ?? $request->input('lead_id');
        
        if ($leadId && in_array($request->method(), ['PUT', 'PATCH', 'DELETE'])) {
            // Check if this is a CRM lead edit/delete operation
            $routeName = $request->route()->getName();
            
            if (str_contains($routeName, 'crm.leads') || str_contains($routeName, 'leads')) {
                // Get the lead model (adjust this based on your CRM lead model)
                $leadModel = null;
                
                // Try to find the lead - adjust model name based on your CRM implementation
                if (class_exists('\App\Models\CrmLead')) {
                    $leadModel = \App\Models\CrmLead::find($leadId);
                } elseif (class_exists('\App\Models\Lead')) {
                    $leadModel = \App\Models\Lead::find($leadId);
                }
                
                if ($leadModel) {
                    // Check if user owns this lead or is assigned to it
                    $isOwner = false;
                    
                    if (isset($leadModel->user_id)) {
                        $isOwner = $leadModel->user_id === $user->id;
                    } elseif (isset($leadModel->assigned_to)) {
                        $isOwner = $leadModel->assigned_to === $user->id;
                    } elseif (isset($leadModel->created_by)) {
                        $isOwner = $leadModel->created_by === $user->id;
                    }
                    
                    if (!$isOwner) {
                        if ($request->expectsJson()) {
                            return response()->json([
                                'error' => 'Forbidden',
                                'message' => 'You can only edit your own CRM leads.'
                            ], 403);
                        }
                        
                        return redirect()->back()
                            ->with('error', 'You can only edit your own CRM leads.');
                    }
                }
            }
        }
        
        return $next($request);
    }
}
