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
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif !important;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', 'Helvetica', sans-serif !important;
            color: var(--text-primary);
            font-size: 10px;
            line-height: 1.4;
            background-color: white;
            padding: 25px 35px;
        }

        /* MAIN CONTAINER */
        .document-container {
            max-width: 100%;
            margin: 0 auto;
        }

        /* HEADER SECTION */
        .header-wrapper {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 25px;
            margin-bottom: 30px;
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
            padding-top: 8px;
        }

        .company-logo {
            margin-bottom: 15px;
        }

        .company-logo img {
            height: 50px;
            max-width: 250px;
            object-fit: contain;
        }

        .company-details {
            font-size: 9px;
            line-height: 1.6;
            color: var(--text-secondary);
        }

        .company-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 6px;
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
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -0.8px;
            margin-bottom: 6px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .document-number {
            font-size: 16px;
            color: var(--text-secondary);
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* META INFORMATION */
        .meta-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            gap: 25px;
            margin-top: 8px;
        }

        .client-section {
            width: 60%;
        }

        .client-info {
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 20px;
            border-left: 3px solid var(--primary-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .section-heading {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .client-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .meta-section {
            width: 35%;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            background-color: var(--light-gray);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .meta-table td {
            padding: 10px 15px;
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
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .meta-table .value {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 10px;
        }

        /* SUBJECT SECTION */
        .subject-section {
            margin-bottom: 25px;
            background-color: var(--primary-light);
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid var(--primary-color);
        }

        .subject-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary-color);
            margin-bottom: 6px;
            font-weight: 700;
        }

        .subject-content {
            font-size: 11px;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ITEMS TABLE */
        .items-section {
            margin-bottom: 25px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
            table-layout: fixed;
            border-spacing: 0;
        }

        .items-table thead {
            background-color: var(--secondary-color);
            color: white;
        }

        .items-table th {
            padding: 12px 10px;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.4px;
            font-weight: 700;
            text-align: left;
            word-wrap: break-word;
            overflow-wrap: break-word;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            box-sizing: border-box;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table th.text-center {
            text-align: center;
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
            padding: 12px 10px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            border-right: 1px solid var(--border-color);
            box-sizing: border-box;
        }

        .items-table td.text-right {
            text-align: right;
        }

        .items-table td.text-center {
            text-align: center;
        }

        .items-table td:last-child,
        .items-table th:last-child {
            border-right: none;
        }

        .item-description {
            font-weight: 500;
            color: var(--text-primary);
        }

        .specifications {
            font-size: 9px;
            color: var(--text-secondary);
            line-height: 1.3;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* TOTALS SECTION - RIGHT ALIGNED */
        .totals-wrapper {
            margin-bottom: 25px;
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            width: 100%;
            margin-left: auto;
        }

        .totals-section {
            width: 320px;
            background-color: var(--light-gray);
            border-radius: 6px;
            padding: 15px;
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-left: auto;
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
            padding: 8px 0;
        }

        .total-label {
            font-weight: 600;
            color: var(--text-secondary);
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .total-amount {
            font-weight: 600;
            color: var(--text-primary);
            text-align: right;
            width: 110px;
            font-size: 11px;
        }

        .grand-total {
            background-color: var(--primary-color);
            color: white;
            border-radius: 4px;
        }

        .grand-total td {
            padding: 12px 10px;
        }

        .grand-total .total-label {
            color: white;
            font-weight: 700;
            font-size: 11px;
        }

        .grand-total .total-amount {
            color: white;
            font-weight: 700;
            font-size: 13px;
        }

        /* TOTAL IN WORDS SECTION */
        .total-words-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f0f9ff;
            border-radius: 6px;
            border-left: 3px solid var(--primary-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .total-words-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-weight: 700;
        }

        .total-words-text {
            font-size: 11px;
            color: var(--text-primary);
            line-height: 1.4;
            font-weight: 600;
            font-style: italic;
        }

        /* CONTENT SECTIONS */
        .content-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: var(--light-gray);
            border-radius: 6px;
            border-left: 3px solid var(--accent-color);
        }

        .content-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .content-text {
            font-size: 10px;
            color: var(--text-primary);
            line-height: 1.5;
        }

        /* BANKING SECTION */
        .banking-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: var(--light-gray);
            border-radius: 6px;
            border-left: 3px solid var(--success-color);
        }

        .banking-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-secondary);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .banking-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .banking-item {
            margin-bottom: 4px;
        }

        .banking-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: var(--text-secondary);
            margin-bottom: 2px;
            font-weight: 600;
        }

        .banking-value {
            font-size: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }

        .full-width {
            grid-column: span 2;
        }

        /* FOOTER */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            color: var(--text-muted);
            font-size: 9px;
        }

        .footer p {
            margin-bottom: 2px;
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
                    <div class="client-name">
                        {{ $quote->customer_name }}
                        @if($customer && $customer->company_name)
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px; font-weight: 500;">
                                {{ $customer->company_name }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Quote Date:</td>
                        <td class="value">{{ formatDubaiDate($quote->quote_date, 'd M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Expiry Date:</td>
                        <td class="value">{{ formatDubaiDate($quote->expiry_date, 'd M Y') }}</td>
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
                        <th style="width: 40%;">Item Description</th>
                        <th style="width: 20%;">Specifications</th>
                        <th style="width: 10%;" class="text-right">Qty</th>
                        <th style="width: 15%;" class="text-right">Rate ({{ $quote->currency ?? 'AED' }})</th>
                        <th style="width: 10%;" class="text-right">Discount</th>
                        <th style="width: 15%;" class="text-right">Amount ({{ $quote->currency ?? 'AED' }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quote->items as $index => $item)
                    <tr>
                        <td class="item-description">
                            {{ $item->item_details }}
                            @if($item->product && $item->product->brand)
                                <div style="font-size: 9px; color: var(--text-secondary); margin-top: 2px;">
                                    <span style="font-weight: 600;">Brand:</span> {{ $item->product->brand->name }}
                                </div>
                            @endif
                        </td>
                        <td class="specifications">
                            @if($item->specifications && !empty(trim($item->specifications)))
                                @php
                                    $selectedSpecs = [];
                                    try {
                                        if (is_string($item->specifications) && (str_starts_with($item->specifications, '[') && str_ends_with($item->specifications, ']'))) {
                                            $selectedSpecs = json_decode($item->specifications, true);
                                        } else {
                                            $selectedSpecs = explode(',', $item->specifications);
                                            $selectedSpecs = array_map('trim', $selectedSpecs);
                                        }
                                    } catch (Exception $e) {
                                        $selectedSpecs = [$item->specifications];
                                    }
                                @endphp
                                
                                @if(count($selectedSpecs) > 0)
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                        @foreach($selectedSpecs as $spec)
                                            <div style="margin-bottom: 2px;">{{ $spec }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                            
                            @if($item->size && !empty(trim($item->size)))
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3; margin-top: 3px;">
                                    <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 1px;">Size:</div>
                                    <div>{{ $item->size }}</div>
                                </div>
                            @endif
                            
                            @if((!$item->specifications || empty(trim($item->specifications))) && (!$item->size || empty(trim($item->size))))
                                <span style="font-size: 9px; color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->rate, 2) }}</td>
                        <td class="text-right">{{ number_format($item->discount, 1) }}%</td>
                        <td class="text-right">{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px; color: var(--text-muted); font-style: italic;">
                            No items added to this quote
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- TOTALS - RIGHT ALIGNED -->
            <div class="totals-wrapper">
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="total-amount">{{ $quote->currency ?? 'AED' }} {{ number_format($quote->sub_total, 2) }}</td>
                        </tr>
                        @if(($quote->shipping_rate ?? 0) > 0)
                        <tr>
                            <td class="total-label">Shipping:</td>
                            <td class="total-amount">{{ $quote->currency ?? 'AED' }} {{ number_format($quote->shipping_rate, 2) }}</td>
                        </tr>
                        @endif
                        @if(($quote->customs_clearance_fee ?? 0) > 0)
                        <tr>
                            <td class="total-label">Customs Clearance:</td>
                            <td class="total-amount">{{ $quote->currency ?? 'AED' }} {{ number_format($quote->customs_clearance_fee, 2) }}</td>
                        </tr>
                        @endif
                        @if(($quote->vat_amount ?? 0) > 0)
                        <tr>
                            <td class="total-label">VAT ({{ number_format($quote->vat_rate ?? 0, 1) }}%):</td>
                            <td class="total-amount">{{ $quote->currency ?? 'AED' }} {{ number_format($quote->vat_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="total-label">Total Amount:</td>
                            <td class="total-amount">{{ $quote->currency ?? 'AED' }} {{ number_format($quote->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- TOTAL IN WORDS -->
        <div class="total-words-section">
            <div class="total-words-title">Amount in Words:</div>
            <div class="total-words-text">{{ numberToWords($quote->total_amount, $quote->currency ?? 'AED') }}</div>
        </div>

        <!-- CUSTOMER NOTES -->
        @if($quote->customer_notes)
        <div class="content-section">
            <div class="content-title">Customer Notes</div>
            <div class="content-text">{{ $quote->customer_notes }}</div>
        </div>
        @endif

        <!-- PAYMENT TERMS -->
        @if(!empty($quote->payment_terms))
        <div class="content-section">
            <div class="content-title">Payment Terms</div>
            <div class="content-text">
                @switch($quote->payment_terms)
                    @case('advance_50')
                        50% advance payment required before processing order
                        @break
                    @case('advance_100')
                        100% advance payment required before processing order
                        @break
                    @case('on_delivery')
                        Payment on delivery
                        @break
                    @case('net_30')
                        Net 30 days from invoice date
                        @break
                    @case('custom')
                        Custom payment terms (as per agreement)
                        @break
                    @default
                        {{ ucfirst(str_replace('_', ' ', $quote->payment_terms)) }}
                @endswitch
            </div>
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
            <p><strong>Quote Valid Until:</strong> {{ formatDubaiDate($quote->expiry_date, 'd M Y') }}</p>
            <p>Generated on {{ nowDubai('d M Y \a\t H:i') }}</p>
        </div>
        </div>
</body>

</html>