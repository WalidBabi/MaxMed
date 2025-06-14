<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        :root {
            --primary-color: #1e40af;
            --primary-light: #dbeafe;
            --secondary-color: #1f2937;
            --accent-color: #0ea5e9;
            --light-gray: #f8fafc;
            --medium-gray: #e2e8f0;
            --border-color: #d1d5db;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            color: var(--text-primary);
            font-size: 11px;
            line-height: 1.5;
            background-color: white;
            padding: 30px 40px;
        }

        /* MAIN CONTAINER */
        .document-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* HEADER SECTION */
        .header-wrapper {
            border-bottom: 3px solid var(--primary-color);
            padding-bottom: 35px;
            margin-bottom: 40px;
            position: relative;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0;
        }

        .company-section {
            width: 60%;
            padding-top: 10px;
        }

        .company-logo {
            margin-bottom: 18px;
        }

        .company-logo img {
            height: 60px;
            max-width: 280px;
            object-fit: contain;
        }

        .company-details {
            font-size: 10px;
            line-height: 1.7;
            color: var(--text-secondary);
        }

        .company-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        .document-title-section {
            width: 35%;
            text-align: right;
            position: absolute;
            top: 0;
            right: 0;
            padding: 0;
        }

        .document-title {
            font-size: 36px;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.8px;
            margin-bottom: 8px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .document-number {
            font-size: 18px;
            color: var(--text-secondary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* META INFORMATION */
        .meta-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            gap: 35px;
            margin-top: 10px;
        }

        .client-section {
            width: 60%;
        }

        .client-info {
            background-color: var(--light-gray);
            border-radius: 10px;
            padding: 25px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .section-heading {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .client-name {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.4;
        }

        .meta-section {
            width: 35%;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--light-gray);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .meta-table td {
            padding: 12px 18px;
            vertical-align: top;
            border-bottom: 1px solid var(--border-color);
        }

        .meta-table tr:last-child td {
            border-bottom: none;
        }

        .meta-table .label {
            font-weight: 600;
            color: var(--text-secondary);
            width: 45%;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .meta-table .value {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 11px;
        }

        /* SUBJECT SECTION */
        .subject-section {
            margin-bottom: 30px;
            background-color: var(--primary-light);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }

        .subject-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .subject-content {
            font-size: 12px;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ITEMS TABLE */
        .items-section {
            margin-bottom: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .items-table thead {
            background-color: var(--secondary-color);
            color: white;
        }

        .items-table th {
            padding: 15px 12px;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            font-weight: 700;
            text-align: left;
        }

        .items-table tbody tr {
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.1s ease;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        .items-table tbody tr:hover {
            background-color: var(--medium-gray);
        }

        .items-table td {
            padding: 15px 12px;
            vertical-align: top;
        }

        .item-description {
            font-weight: 500;
            color: var(--text-primary);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* TOTALS SECTION - RIGHT ALIGNED */
        .totals-wrapper {
            margin-bottom: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .totals-section {
            width: 350px;
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid var(--border-color);
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table tr {
            border-bottom: 1px solid var(--border-color);
        }

        .totals-table tr:last-child {
            border-bottom: none;
        }

        .totals-table td {
            padding: 10px 0;
        }

        .total-label {
            font-weight: 600;
            color: var(--text-secondary);
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .total-amount {
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
            width: 120px;
            font-size: 12px;
        }

        .grand-total {
            background-color: var(--primary-color);
            color: white;
            border-radius: 6px;
        }

        .grand-total td {
            padding: 15px 12px;
        }

        .grand-total .total-label {
            color: white;
            font-weight: 700;
            font-size: 12px;
        }

        .grand-total .total-amount {
            color: white;
            font-weight: 700;
            font-size: 14px;
        }

        /* CONTENT SECTIONS */
        .content-section {
            margin-bottom: 25px;
            padding: 20px;
            background-color: var(--light-gray);
            border-radius: 8px;
            border-left: 4px solid var(--accent-color);
        }

        .content-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .content-text {
            font-size: 11px;
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* BANKING SECTION */
        .banking-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: var(--light-gray);
            border-radius: 8px;
            border-left: 4px solid var(--success-color);
        }

        .banking-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .banking-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .banking-item {
            margin-bottom: 5px;
        }

        .banking-label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            margin-bottom: 3px;
            font-weight: 600;
        }

        .banking-value {
            font-size: 11px;
            color: var(--text-primary);
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        .full-width {
            grid-column: span 2;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid var(--border-color);
            text-align: center;
            color: var(--text-muted);
            font-size: 10px;
        }

        .footer p {
            margin-bottom: 3px;
        }

        .footer strong {
            color: var(--text-secondary);
        }

        /* RESPONSIVE ADJUSTMENTS */
        @media print {
            body {
                padding: 20px 30px;
            }
            
            .items-table tbody tr:hover {
                background-color: inherit;
            }
        }
    </style>
</head>

<body>
    <div class="document-container">
        <!-- HEADER WITH COMPANY INFO & DOCUMENT TITLE -->
        <div class="header-wrapper">
    <div class="header-section">
        <div class="company-section">
            <div class="company-logo">
                <img src="{{ public_path('Images/logo.png') }}" alt="MaxMed Logo">
            </div>
            <div class="company-details">
                        <div class="company-name">MaxMed Scientific and Laboratory Equipment Trading Co. LLC</div>
                <div>Dubai 448945</div>
                <div>United Arab Emirates</div>
                <div>sales@maxmedme.com</div>
                <div>www.maxmedme.com</div>
            </div>
                </div>
                
        <div class="document-title-section">
            <div class="document-title">Quote</div>
            <div class="document-number">{{ $quote->quote_number }}</div>
        </div>
            </div>
        </div>

        <!-- CLIENT INFO & META DATA -->
        <div class="meta-wrapper">
            <div class="client-section">
                <div class="client-info">
                    <div class="section-heading">Bill To</div>
                    <div class="client-name">{{ $quote->customer_name }}</div>
                </div>
            </div>

            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Quote Date:</td>
                        <td class="value">{{ $quote->quote_date->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Expiry Date:</td>
                        <td class="value">{{ $quote->expiry_date->format('d M Y') }}</td>
                    </tr>
                    @if($quote->reference_number)
                    <tr>
                        <td class="label">Reference:</td>
                        <td class="value">{{ $quote->reference_number }}</td>
                    </tr>
                    @endif
                    @if($quote->salesperson)
                    <tr>
                        <td class="label">Salesperson:</td>
                        <td class="value">{{ $quote->salesperson }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- SUBJECT SECTION -->
        @if($quote->subject)
        <div class="subject-section">
            <div class="subject-title">Subject</div>
            <div class="subject-content">{{ $quote->subject }}</div>
        </div>
        @endif

        <!-- ITEMS TABLE -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Item Description</th>
                        <th style="width: 12%;" class="text-right">Qty</th>
                        <th style="width: 15%;" class="text-right">Rate (AED)</th>
                        <th style="width: 10%;" class="text-right">Discount</th>
                        <th style="width: 13%;" class="text-right">Amount (AED)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quote->items as $item)
                    <tr>
                        <td class="item-description">{{ $item->item_details }}</td>
                        <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($item->discount, 1) }}%</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 30px; color: var(--text-muted); font-style: italic;">
                            No items added to this quote
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TOTALS - RIGHT ALIGNED -->
        <div class="totals-wrapper">
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                        <td class="total-amount">AED {{ number_format($quote->sub_total, 2) }}</td>
                </tr>
                <tr class="grand-total">
                        <td class="total-label">Total Amount:</td>
                        <td class="total-amount">AED {{ number_format($quote->total_amount, 2) }}</td>
                </tr>
            </table>
            </div>
        </div>

        <!-- CUSTOMER NOTES -->
        @if($quote->customer_notes)
        <div class="content-section">
            <div class="content-title">Customer Notes</div>
            <div class="content-text">{{ $quote->customer_notes }}</div>
        </div>
        @endif

        <!-- TERMS & CONDITIONS -->
        @if(!empty(trim($quote->terms_conditions)))
        <div class="content-section">
            <div class="content-title">Terms & Conditions</div>
            <div class="content-text">{{ $quote->terms_conditions }}</div>
        </div>
        @endif


        <!-- FOOTER -->
        <div class="footer">
            <p><strong>Quote Valid Until:</strong> {{ $quote->expiry_date->format('d M Y') }}</p>
            <p>Generated on {{ now()->format('d M Y \a\t H:i') }}</p>
        </div>
        </div>
</body>

</html>