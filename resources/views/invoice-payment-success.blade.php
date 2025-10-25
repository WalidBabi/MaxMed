<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment {{ isset($error) ? 'Processing' : 'Successful' }} - MaxMed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px;
            text-align: center;
            max-width: 600px;
        }
        .success-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .success-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .success-message {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .invoice-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #6c757d;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-card">
        @if(isset($error))
            <div class="success-icon">⏳</div>
            <h1 class="success-title">Payment Processing</h1>
        @else
            <div class="success-icon">✅</div>
            <h1 class="success-title">Payment Successful!</h1>
        @endif
        
        <p class="success-message">{{ $message }}</p>
        
        @if(isset($invoice))
        <div class="invoice-details">
            <div class="detail-row">
                <span class="detail-label">Invoice Number:</span>
                <span class="detail-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Invoice Type:</span>
                <span class="detail-value">{{ $invoice->type === 'proforma' ? 'Proforma Invoice' : 'Final Invoice' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value">{{ $invoice->currency ?? 'AED' }} {{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Paid Amount:</span>
                <span class="detail-value">{{ $invoice->currency ?? 'AED' }} {{ number_format($invoice->paid_amount ?? 0, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Status:</span>
                <span class="detail-value">
                    <span class="badge bg-{{ $invoice->payment_status === 'paid' ? 'success' : 'warning' }}">
                        {{ ucfirst($invoice->payment_status) }}
                    </span>
                </span>
            </div>
        </div>
        @endif
        
        <div class="mt-4">
            <a href="{{ url('/') }}" class="btn-home">Return to Homepage</a>
        </div>
        
        <p class="mt-4 text-muted" style="font-size: 14px;">
            A confirmation email has been sent to your registered email address.
            <br>
            For any questions, please contact us at <a href="mailto:sales@maxmedme.com">sales@maxmedme.com</a>
        </p>
    </div>
</body>
</html>

