<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // If user is a supplier, redirect to onboarding
            if ($request->user()->isSupplier()) {
                return Redirect::route('supplier.onboarding.company')
                    ->with('success', 'Email already verified. Please complete your supplier profile.');
            }

            // For other users, redirect to dashboard
            return Redirect::intended(route('dashboard'))
                ->with('success', 'Email already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
