<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Log all exceptions with detailed information
            $this->logException($e);
        });

        // Handle specific exceptions
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleException($e, $request);
        });
    }

    /**
     * Log exception with detailed information
     */
    protected function logException(Throwable $e): void
    {
        $logData = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'class' => get_class($e),
            'timestamp' => now()->toISOString(),
            'url' => request()->url(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'is_authenticated' => auth()->check(),
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'environment' => app()->environment(),
        ];

        // Add request data if available
        if (request()->has('email')) {
            $logData['email'] = request()->input('email');
        }

        // Add database connection status
        try {
            \DB::connection()->getPdo();
            $logData['database_status'] = 'connected';
        } catch (\Exception $dbError) {
            $logData['database_status'] = 'disconnected';
            $logData['database_error'] = $dbError->getMessage();
        }

        // Log to multiple channels for redundancy
        Log::error('Unhandled Exception: ' . $e->getMessage(), $logData);
        
        // Also write to a dedicated error log file
        $this->writeToErrorLog($logData);
    }

    /**
     * Write error to dedicated log file
     */
    protected function writeToErrorLog(array $logData): void
    {
        try {
            $logFile = storage_path('logs/critical-errors.log');
            $logEntry = sprintf(
                "[%s] %s in %s:%d\nURL: %s\nMethod: %s\nIP: %s\nUser: %s\nSession: %s\nTrace:\n%s\n\n",
                now()->format('Y-m-d H:i:s'),
                $logData['message'],
                $logData['file'],
                $logData['line'],
                $logData['url'],
                $logData['method'],
                $logData['ip'],
                $logData['is_authenticated'] ? 'Authenticated (ID: ' . $logData['user_id'] . ')' : 'Not authenticated',
                $logData['session_id'],
                $logData['trace']
            );

            File::append($logFile, $logEntry);
        } catch (\Exception $writeError) {
            // If we can't write to the log file, at least try to write to Laravel's main log
            Log::error('Failed to write to critical error log: ' . $writeError->getMessage());
        }
    }

    /**
     * Handle specific exceptions
     */
    protected function handleException(Throwable $e, $request)
    {
        // Log the exception first
        $this->logException($e);

        // Handle specific exception types
        if ($e instanceof AuthenticationException) {
            Log::warning('Authentication Exception', [
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
        }

        if ($e instanceof ValidationException) {
            Log::warning('Validation Exception', [
                'url' => $request->url(),
                'errors' => $e->errors()
            ]);
        }

        if ($e instanceof QueryException) {
            Log::error('Database Query Exception', [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'url' => $request->url()
            ]);
        }

        if ($e instanceof ModelNotFoundException) {
            Log::warning('Model Not Found Exception', [
                'model' => $e->getModel(),
                'url' => $request->url()
            ]);
        }

        // For production, return a generic error page
        if (app()->environment('production')) {
            return response()->view('errors.500', [], 500);
        }

        // For development, let Laravel handle it normally
        return null;
    }

    /**
     * Convert an authentication exception into a response.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        Log::warning('Unauthenticated access attempt', [
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'guards' => $exception->guards()
        ]);

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
} 