<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Receipt - {{ $cashReceipt->receipt_number }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .header {
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 12px;
            color: #6B7280;
            margin-bottom: 10px;
        }
        
        .company-details {
            font-size: 11px;
            color: #6B7280;
            line-height: 1.4;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #1F2937;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .receipt-number {
            text-align: center;
            font-size: 16px;
            color: #4F46E5;
            font-weight: bold;
            margin-bottom: 30px;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 12px;
            border-bottom: 1px dotted #E5E7EB;
            padding-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #374151;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #1F2937;
            flex: 1;
        }
        
        .customer-section, .payment-section {
            background-color: #F9FAFB;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4F46E5;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .amount-section {
            background-color: #FEF3C7;
            border: 2px solid #F59E0B;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        
        .amount-label {
            font-size: 14px;
            color: #92400E;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #92400E;
        }
        
        .payment-method {
            display: inline-block;
            background-color: #E0E7FF;
            color: #3730A3;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-issued {
            background-color: #D1FAE5;
            color: #065F46;
        }
        
        .status-draft {
            background-color: #FEF3C7;
            color: #92400E;
        }
        
        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        
        .footer {
            margin-top: 50px;
            border-top: 2px solid #E5E7EB;
            padding-top: 20px;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
            width: 200px;
        }
        
        .signature-line {
            border-bottom: 2px solid #374151;
            margin-bottom: 10px;
            height: 40px;
        }
        
        .signature-label {
            font-size: 12px;
            color: #6B7280;
            font-weight: bold;
        }
        
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #F3F4F6;
            border-radius: 6px;
            border-left: 3px solid #6B7280;
        }
        
        .notes-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .thank-you {
            text-align: center;
            margin-top: 30px;
            font-style: italic;
            color: #6B7280;
            font-size: 16px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .header {
                page-break-inside: avoid;
            }
            
            .amount-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-info">
            <div class="company-name">MaxMed</div>
            <div class="company-tagline">Your Trusted Laboratory & Medical Equipment Partner</div>
            <div class="company-details">
                Office: Dubai, UAE | Email: info@maxmed.ae | Phone: +971-XX-XXX-XXXX<br>
                Website: www.maxmed.ae | Laboratory & Medical Equipment Solutions
            </div>
        </div>
    </div>

    <!-- Receipt Title -->
    <div class="receipt-title">Cash Receipt</div>
    <div class="receipt-number">{{ $cashReceipt->receipt_number }}</div>

    <!-- Customer Information -->
    <div class="customer-section">
        <div class="section-title">Customer Information</div>
        <div class="info-row">
            <div class="info-label">Customer Name:</div>
            <div class="info-value">{{ $cashReceipt->customer_name }}</div>
        </div>
        @if($cashReceipt->customer_email)
        <div class="info-row">
            <div class="info-label">Email:</div>
            <div class="info-value">{{ $cashReceipt->customer_email }}</div>
        </div>
        @endif
        @if($cashReceipt->customer_phone)
        <div class="info-row">
            <div class="info-label">Phone:</div>
            <div class="info-value">{{ $cashReceipt->customer_phone }}</div>
        </div>
        @endif
        @if($cashReceipt->customer_address)
        <div class="info-row">
            <div class="info-label">Address:</div>
            <div class="info-value">{{ $cashReceipt->customer_address }}</div>
        </div>
        @endif
    </div>

    <!-- Payment Information -->
    <div class="payment-section">
        <div class="section-title">Payment Details</div>
        <div class="info-row">
            <div class="info-label">Receipt Date:</div>
            <div class="info-value">{{ formatDubaiDate($cashReceipt->receipt_date, 'F j, Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Payment Method:</div>
            <div class="info-value">
                <span class="payment-method">{{ ucfirst(str_replace('_', ' ', $cashReceipt->payment_method)) }}</span>
            </div>
        </div>
        @if($cashReceipt->reference_number)
        <div class="info-row">
            <div class="info-label">Reference Number:</div>
            <div class="info-value">{{ $cashReceipt->reference_number }}</div>
        </div>
        @endif
        @if($cashReceipt->order)
        <div class="info-row">
            <div class="info-label">Related Order:</div>
            <div class="info-value">{{ $cashReceipt->order->order_number }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">
                <span class="status-badge status-{{ $cashReceipt->status }}">{{ ucfirst($cashReceipt->status) }}</span>
            </div>
        </div>
    </div>

    <!-- Amount Section -->
    <div class="amount-section">
        <div class="amount-label">Total Amount Received</div>
        <div class="amount-value">{{ number_format($cashReceipt->amount, 2) }} {{ $cashReceipt->currency }}</div>
    </div>

    <!-- Description -->
    @if($cashReceipt->description)
    <div class="notes">
        <div class="notes-title">Description:</div>
        {{ $cashReceipt->description }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div class="info-row">
            <div class="info-label">Issued By:</div>
            <div class="info-value">{{ $cashReceipt->user->name ?? 'System' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Issue Date:</div>
            <div class="info-value">{{ formatDubaiDate($cashReceipt->created_at, 'F j, Y \a\t g:i A') }}</div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Customer Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">Authorized Signature</div>
            </div>
        </div>

        <div class="thank-you">
            Thank you for your business!
        </div>
    </div>
</body>
</html> 