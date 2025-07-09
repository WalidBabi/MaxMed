<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        Log::info('LoginRequest::authenticate() - Starting authentication process', [
            'email' => $this->email,
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()->toISOString(),
            'session_id' => session()->getId(),
            'auth_guard' => config('auth.defaults.guard')
        ]);

        $this->ensureIsNotRateLimited();

        Log::info('LoginRequest::authenticate() - Rate limiting check passed', [
            'email' => $this->email,
            'ip' => $this->ip()
        ]);

        try {
            $credentials = $this->only('email', 'password');
            $remember = $this->boolean('remember');
            
            Log::info('LoginRequest::authenticate() - Attempting authentication', [
                'email' => $this->email,
                'has_password' => !empty($this->password),
                'remember' => $remember,
                'credentials_keys' => array_keys($credentials),
                'auth_model' => config('auth.providers.users.model')
            ]);

            if (! Auth::attempt($credentials, $remember)) {
                Log::warning('LoginRequest::authenticate() - Authentication failed', [
                    'email' => $this->email,
                    'ip' => $this->ip(),
                    'user_agent' => $this->userAgent(),
                    'timestamp' => now()->toISOString(),
                    'credentials_provided' => array_keys($credentials)
                ]);

                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }

            Log::info('LoginRequest::authenticate() - Authentication successful', [
                'email' => $this->email,
                'user_id' => Auth::id(),
                'timestamp' => now()->toISOString(),
                'auth_check' => Auth::check()
            ]);

            RateLimiter::clear($this->throttleKey());

        } catch (\Exception $e) {
            Log::error('LoginRequest::authenticate() - Exception during authentication', [
                'email' => $this->email,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toISOString(),
                'exception_type' => get_class($e)
            ]);
            throw $e;
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        Log::info('LoginRequest::ensureIsNotRateLimited() - Checking rate limits', [
            'email' => $this->email,
            'ip' => $this->ip(),
            'throttle_key' => $this->throttleKey()
        ]);

        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            Log::info('LoginRequest::ensureIsNotRateLimited() - Rate limit check passed', [
                'email' => $this->email,
                'attempts' => RateLimiter::attempts($this->throttleKey())
            ]);
            return;
        }

        Log::warning('LoginRequest::ensureIsNotRateLimited() - Rate limit exceeded', [
            'email' => $this->email,
            'ip' => $this->ip(),
            'attempts' => RateLimiter::attempts($this->throttleKey()),
            'available_in' => RateLimiter::availableIn($this->throttleKey())
        ]);

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        $key = Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
        
        Log::debug('LoginRequest::throttleKey() - Generated throttle key', [
            'email' => $this->email,
            'ip' => $this->ip(),
            'throttle_key' => $key
        ]);
        
        return $key;
    }
}
