<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->getRedirectResponse($request->user());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->getRedirectResponse($request->user());
    }

    /**
     * Get the redirect response based on user type.
     */
    private function getRedirectResponse($user): RedirectResponse
    {
        // Debug logging
        Log::info('Email verification redirect debug', [
            'user_id' => $user->id,
            'email_verified' => $user->hasVerifiedEmail(),
            'is_supplier' => $user->isSupplier(),
            'role_name' => $user->role ? $user->role->name : 'no role',
            'auth_check' => Auth::check(),
            'current_user_id' => Auth::id()
        ]);

        // Set verification redirect flag in session
        Session::put('verification_redirect', true);

        // If user is a supplier, redirect to onboarding
        if ($user->isSupplier()) {
            Log::info('Redirecting supplier to onboarding after email verification', ['user_id' => $user->id]);
            return Redirect::route('supplier.onboarding.company')
                ->with('success', 'Email verified successfully! Please complete your supplier profile.');
        }

        // For other users, redirect to dashboard
        Log::info('Redirecting non-supplier to dashboard after email verification', ['user_id' => $user->id]);
        return Redirect::intended(route('dashboard'))
            ->with('success', 'Email verified successfully!');
    }
}
