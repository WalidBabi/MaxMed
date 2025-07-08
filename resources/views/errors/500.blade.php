<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - MaxMed UAE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .error-container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            text-align: center;
        }
        .error-code {
            font-size: 72px;
            color: #dc3545;
            margin: 0;
            font-weight: bold;
        }
        .error-title {
            font-size: 24px;
            color: #333;
            margin: 20px 0;
        }
        .error-message {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 4px;
            text-align: left;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">500</h1>
        <h2 class="error-title">Internal Server Error</h2>
        <p class="error-message">
            We're experiencing technical difficulties. Our team has been notified and is working to resolve the issue.
        </p>
        
        @if(app()->environment('local'))
        <div class="error-details">
            <strong>Debug Information:</strong><br>
            <strong>Environment:</strong> {{ app()->environment() }}<br>
            <strong>Time:</strong> {{ now()->format('Y-m-d H:i:s') }}<br>
            <strong>URL:</strong> {{ request()->url() }}<br>
            <strong>Method:</strong> {{ request()->method() }}<br>
            <strong>IP:</strong> {{ request()->ip() }}<br>
            <strong>User Agent:</strong> {{ request()->userAgent() }}<br>
            <strong>Session ID:</strong> {{ session()->getId() }}<br>
            <strong>Database Status:</strong> 
            @try
                @if(DB::connection()->getPdo())
                    Connected
                @else
                    Disconnected
                @endif
            @catch(Exception $e)
                Error: {{ $e->getMessage() }}
            @endtry
        </div>
        @endif

        <div class="debug-info">
            <strong>For Administrators:</strong><br>
            Check the following log files for detailed error information:<br>
            • <code>storage/logs/laravel.log</code><br>
            • <code>storage/logs/critical-errors.log</code><br>
            • <code>storage/logs/production-debug.log</code>
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ url('/') }}" class="btn">Go Home</a>
            <a href="{{ route('login') }}" class="btn btn-secondary">Try Login Again</a>
        </div>
    </div>

    @if(app()->environment('local'))
    <script>
        // Add some basic error reporting for development
        console.log('500 Error Details:', {
            url: '{{ request()->url() }}',
            method: '{{ request()->method() }}',
            timestamp: '{{ now()->toISOString() }}',
            environment: '{{ app()->environment() }}'
        });
    </script>
    @endif
</body>
</html> 