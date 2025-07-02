<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Receipt {{ $cashReceipt->receipt_number }}</title>
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

        .client-address {
            font-size: 10px;
            color: var(--text-secondary);
            line-height: 1.5;
            margin-top: 6px;
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

        /* PAYMENT DETAILS */
        .payment-details {
            margin-top: 30px;
            background-color: var(--primary-light);
            border-radius: 8px;
            padding: 20px;
            border-left: 3px solid var(--primary-color);
        }

        .payment-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .amount-section {
            text-align: right;
            margin-top: 20px;
            padding: 15px;
            background-color: var(--light-gray);
            border-radius: 8px;
        }

        .amount-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .amount-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }

        /* Related Documents */
        .related-documents {
            margin-top: 30px;
        }

        .related-documents table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .related-documents table td {
            padding: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--light-gray);
        }

        .related-documents table td:first-child {
            width: 25%;
        }

        .related-documents table td:last-child {
            font-weight: 600;
        }

        /* Delivery Signature */
        .delivery-signature {
            margin-top: 30px;
        }

        .delivery-signature .signature-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .delivery-signature .signature-image {
            text-align: center;
        }

        .delivery-signature .signature-image img {
            max-width: 300px;
            max-height: 100px;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 8px;
            height: 40px;
        }

        .signature-label {
            font-size: 9px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .notes {
            margin-top: 20px;
            font-size: 9px;
            color: var(--text-secondary);
            text-align: center;
            font-style: italic;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-issued {
            background-color: #dcfce7;
            color: var(--success-color);
        }

        .status-draft {
            background-color: #fef3c7;
            color: var(--warning-color);
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: var(--danger-color);
        }

        /* RESPONSIVE ADJUSTMENTS */
        @media print {
            body {
                padding: 20px 30px;
            }
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
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Header -->
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
                    <div class="document-title">Cash Receipt</div>
                    <div class="document-number">{{ $cashReceipt->receipt_number }}</div>
                </div>
            </div>
        </div>

        <!-- Meta Information -->
        <div class="meta-wrapper">
            <div class="client-section">
                <div class="client-info">
                    <div class="section-heading">Customer Information</div>
                    <div class="client-name">{{ $cashReceipt->customer_name }}</div>
                    @if($customer && $customer->company_name)
                        <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px; font-weight: 500;">
                            {{ $customer->company_name }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Receipt Date</td>
                        <td class="value">{{ formatDubaiDate($cashReceipt->payment_date, 'M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Method</td>
                        <td class="value">{{ ucfirst(str_replace('_', ' ', $cashReceipt->payment_method)) }}</td>
                    </tr>
                    @if($cashReceipt->reference_number)
                    <tr>
                        <td class="label">Reference</td>
                        <td class="value">{{ $cashReceipt->reference_number }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <div class="payment-title">Payment Details</div>
            @if($cashReceipt->description)
            <div style="margin-bottom: 15px; font-size: 10px;">{{ $cashReceipt->description }}</div>
            @endif
            <div class="amount-section">
                <div class="amount-label">Amount Received</div>
                <div class="amount-value">{{ number_format($cashReceipt->amount, 2) }} {{ $cashReceipt->currency }}</div>
            </div>
        </div>

        <!-- Related Documents -->
        @if($cashReceipt->order)
        <div class="related-documents">
            <div class="payment-title">Related Documents</div>
            <table>
                <tr>
                    <td>Order Number:</td>
                    <td>{{ $cashReceipt->order->order_number }}</td>
                </tr>
                @if($cashReceipt->order->delivery)
                <tr>
                    <td>Delivery Number:</td>
                    <td>{{ $cashReceipt->order->delivery->tracking_number }}</td>
                </tr>
                @endif
                @if($cashReceipt->order->proformaInvoice)
                <tr>
                    <td>Proforma Invoice:</td>
                    <td>{{ $cashReceipt->order->proformaInvoice->invoice_number }}</td>
                </tr>
                @endif
                @if($cashReceipt->order->delivery && $cashReceipt->order->delivery->finalInvoice)
                <tr>
                    <td>Final Invoice:</td>
                    <td>{{ $cashReceipt->order->delivery->finalInvoice->invoice_number }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Products List -->
        <div style="margin-top: 30px;"></div>
        @if($cashReceipt->order->items->count() > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Item Description</th>
                        <th style="width: 20%;">Specifications</th>
                        <th style="width: 10%;" class="text-center">Quantity</th>
                        <th style="width: 15%;" class="text-right">Unit Price ({{ $cashReceipt->currency }})</th>
                        <th style="width: 15%;" class="text-right">Amount ({{ $cashReceipt->currency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cashReceipt->order->items as $item)
                    <tr>
                        <td class="item-description">
                            {{ $item->product->name }}
                            @if($item->product && $item->product->brand)
                                <div style="font-size: 9px; color: var(--text-secondary); margin-top: 3px;">
                                    <span class="font-medium">Brand:</span> {{ $item->product->brand->name }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @php
                                // Get invoice item for this order item to get specifications and size
                                $invoiceItem = null;
                                if($cashReceipt->order && $cashReceipt->order->invoice) {
                                    $invoiceItem = $cashReceipt->order->invoice->items->where('product_id', $item->product_id)->first();
                                }
                            @endphp
                            
                            @if($invoiceItem && $invoiceItem->specifications)
                                @php
                                    $selectedSpecs = json_decode($invoiceItem->specifications, true);
                                @endphp
                                @if(count($selectedSpecs) > 0)
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                        @foreach($selectedSpecs as $spec)
                                            <div style="margin-bottom: 2px;">{{ $spec }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                            
                            @if($invoiceItem && $invoiceItem->size && !empty(trim($invoiceItem->size)))
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3; margin-top: 3px;">
                                    <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 1px;">Size:</div>
                                    <div>{{ $invoiceItem->size }}</div>
                                </div>
                            @endif
                            
                            @if((!$invoiceItem || !$invoiceItem->specifications || empty(trim($invoiceItem->specifications))) && (!$invoiceItem || !$invoiceItem->size || empty(trim($invoiceItem->size))))
                                <span style="font-size: 9px; color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals Section -->
            <div class="totals-wrapper">
                <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td class="total-label">Subtotal:</td>
                            <td class="total-amount">{{ $cashReceipt->currency }} {{ number_format($cashReceipt->order->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }}</td>
                        </tr>
                        @if($cashReceipt->order->tax_amount > 0)
                        <tr>
                            <td class="total-label">Tax:</td>
                            <td class="total-amount">{{ $cashReceipt->currency }} {{ number_format($cashReceipt->order->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($cashReceipt->order->discount_amount > 0)
                        <tr>
                            <td class="total-label">Discount:</td>
                            <td class="total-amount">-{{ $cashReceipt->currency }} {{ number_format($cashReceipt->order->discount_amount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="total-label">Total Amount:</td>
                            <td class="total-amount">{{ $cashReceipt->currency }} {{ number_format($cashReceipt->order->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Delivery Signature -->
        @if($cashReceipt->order && $cashReceipt->order->delivery && $cashReceipt->order->delivery->customer_signature)
        <div class="delivery-signature">
            <div class="payment-title">Delivery Confirmation</div>
            <div style="margin-bottom: 10px;">
                <span style="font-weight: 600;">Signed at:</span>
                <span>{{ formatDubaiDate($cashReceipt->order->delivery->signed_at, 'M d, Y H:i') }}</span>
            </div>
            <div class="signature-image" style="text-align: left;">
                <img src="{{ public_path('storage/' . $cashReceipt->order->delivery->customer_signature) }}" alt="Customer Signature" style="max-width: 300px; max-height: 100px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
        </div>
        @endif

        @endif


    </div>
</body>
</html>