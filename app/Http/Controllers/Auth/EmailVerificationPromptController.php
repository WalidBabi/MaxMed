<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Debug logging
        \Log::info('Email verification prompt debug', [
            'user_id' => $request->user()->id,
            'email_verified' => $request->user()->hasVerifiedEmail(),
            'is_supplier' => $request->user()->isSupplier(),
            'role_name' => $request->user()->role ? $request->user()->role->name : 'no role',
            'auth_check' => \Auth::check(),
            'current_user_id' => \Auth::id()
        ]);

        if ($request->user()->hasVerifiedEmail()) {
            // If user is a supplier, redirect to onboarding
            if ($request->user()->isSupplier()) {
                \Log::info('Redirecting verified supplier to onboarding from prompt', ['user_id' => $request->user()->id]);
                return Redirect::route('supplier.onboarding.company')
                    ->with('success', 'Email already verified. Please complete your supplier profile.');
            }

            // For other users, redirect to dashboard
            \Log::info('Redirecting verified non-supplier to dashboard from prompt', ['user_id' => $request->user()->id]);
            return Redirect::intended(route('dashboard'))
                ->with('success', 'Email already verified.');
        }

        \Log::info('Showing email verification notice', ['user_id' => $request->user()->id]);
        return view('auth.verify-email');
    }
}
