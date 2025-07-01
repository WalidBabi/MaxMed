<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
    <style>
        /* CSS Variables for consistent theming */
        :root {
            --primary-color: #1f2937;
            --secondary-color: #6b7280;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --text-primary: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --border-color: #d1d5db;
            --background-light: #f9fafb;
            --background-white: #ffffff;
        }

        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: var(--text-primary);
            background-color: var(--background-white);
        }

        /* Document Container */
        .document-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: var(--background-white);
        }

        /* Header Styles */
        .header-wrapper {
            margin-bottom: 30px;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .company-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .company-logo {
            width: 80px;
            height: 80px;
        }

        .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .company-details {
            font-size: 11px;
            line-height: 1.3;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 3px;
        }

        .document-title-section {
            text-align: right;
        }

        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: var(--accent-color);
            margin-bottom: 5px;
        }

        .document-number {
            font-size: 16px;
            font-weight: bold;
            color: var(--primary-color);
        }

        /* Meta Information */
        .meta-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
        }

        .supplier-section {
            flex: 1;
        }

        .meta-section {
            flex: 1;
            text-align: right;
        }

        .section-heading {
            font-size: 12px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .supplier-name {
            font-size: 14px;
            font-weight: bold;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .supplier-details {
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 4px 0;
            font-size: 11px;
        }

        .meta-table .label {
            font-weight: 600;
            color: var(--text-secondary);
            text-align: left;
            padding-right: 15px;
        }

        .meta-table .value {
            color: var(--text-primary);
            text-align: right;
            font-weight: 500;
        }

        /* Description Section */
        .description-section {
            margin-bottom: 25px;
            padding: 15px;
            background-color: var(--background-light);
            border-radius: 5px;
        }

        .description-title {
            font-size: 12px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .description-content {
            font-size: 11px;
            color: var(--text-primary);
            line-height: 1.4;
        }

        /* Items Table */
        .items-section {
            margin-bottom: 25px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid var(--border-color);
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: var(--background-light);
            padding: 10px 8px;
            font-size: 10px;
            font-weight: bold;
            color: var(--primary-color);
            text-align: left;
            border-bottom: 2px solid var(--border-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 8px;
            font-size: 10px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* Totals Section */
        .totals-wrapper {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
        }

        .totals-section {
            width: 300px;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 0;
            font-size: 11px;
        }

        .total-label {
            font-weight: 600;
            color: var(--text-secondary);
            text-align: left;
        }

        .total-amount {
            font-weight: bold;
            color: var(--text-primary);
            text-align: right;
        }

        .grand-total {
            border-top: 2px solid var(--border-color);
            padding-top: 8px;
            margin-top: 8px;
        }

        .grand-total .total-label {
            font-size: 13px;
            color: var(--primary-color);
        }

        .grand-total .total-amount {
            font-size: 13px;
            color: var(--primary-color);
        }

        /* Content Sections */
        .content-section {
            margin-bottom: 20px;
        }

        .content-title {
            font-size: 12px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-text {
            font-size: 11px;
            color: var(--text-primary);
            line-height: 1.4;
            white-space: pre-line;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 10px;
            color: var(--text-muted);
        }

        .footer p {
            margin-bottom: 5px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .status-sent_to_supplier {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .status-acknowledged {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-in_production {
            background-color: #e9d5ff;
            color: #7c3aed;
        }

        .status-completed {
            background-color: #d1fae5;
            color: #059669;
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
                    <div class="document-title">Purchase Order</div>
                    <div class="document-number">{{ $purchaseOrder->po_number }}</div>
                </div>
            </div>
        </div>

        <!-- SUPPLIER INFO & META DATA -->
        <div class="meta-wrapper">
            <div class="supplier-section">
                <div class="section-heading">Supplier Information</div>
                <div class="supplier-name">{{ $purchaseOrder->supplier_name }}</div>
                <div class="supplier-details">
                    @if($purchaseOrder->supplier_email)
                        <div>{{ $purchaseOrder->supplier_email }}</div>
                    @endif
                    @if($purchaseOrder->supplier_phone)
                        <div>{{ $purchaseOrder->supplier_phone }}</div>
                    @endif
                    @if($purchaseOrder->supplier_address)
                        <div>{{ $purchaseOrder->supplier_address }}</div>
                    @endif
                </div>
            </div>

            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">PO Date:</td>
                        <td class="value">{{ formatDubaiDate($purchaseOrder->po_date, 'd M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Delivery Date:</td>
                        <td class="value">{{ formatDubaiDate($purchaseOrder->delivery_date_requested, 'd M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status:</td>
                        <td class="value">
                            <span class="status-badge status-{{ $purchaseOrder->status }}">
                                {{ \App\Models\PurchaseOrder::$statuses[$purchaseOrder->status] ?? ucfirst($purchaseOrder->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label">Payment Status:</td>
                        <td class="value">
                            <span class="status-badge status-{{ $purchaseOrder->payment_status }}">
                                {{ \App\Models\PurchaseOrder::$paymentStatuses[$purchaseOrder->payment_status] ?? ucfirst($purchaseOrder->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @if($purchaseOrder->order)
                    <tr>
                        <td class="label">Customer Order:</td>
                        <td class="value">{{ $purchaseOrder->order->order_number }}</td>
                    </tr>
                    @endif
                    @if($purchaseOrder->supplier_quotation_id)
                    <tr>
                        <td class="label">Quotation:</td>
                        <td class="value">#{{ $purchaseOrder->supplier_quotation_id }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- DESCRIPTION SECTION -->
        @if($purchaseOrder->description)
        <div class="description-section">
            <div class="description-title">Description</div>
            <div class="description-content">{{ $purchaseOrder->description }}</div>
        </div>
        @endif

        <!-- ITEMS TABLE -->
        @if($purchaseOrder->items->count() > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">Item Description</th>
                        <th style="width: 20%;" class="text-center">Specifications</th>
                        <th style="width: 10%;" class="text-center">Quantity</th>
                        <th style="width: 15%;" class="text-right">Unit Price ({{ $purchaseOrder->currency ?? 'AED' }})</th>
                        <th style="width: 10%;" class="text-center">Discount</th>
                        <th style="width: 20%;" class="text-right">Line Total ({{ $purchaseOrder->currency ?? 'AED' }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $index => $item)
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 3px;">
                                {{ $item->item_description }}
                            </div>
                            @if($item->product && $item->product->brand)
                                <div style="font-size: 9px; color: var(--text-secondary);">
                                    Brand: {{ $item->product->brand->name }}
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->specifications)
                                @php
                                    $specs = is_string($item->specifications) ? json_decode($item->specifications, true) : $item->specifications;
                                    $specs = is_array($specs) ? $specs : [];
                                @endphp
                                @if(count($specs) > 0)
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                        @foreach($specs as $spec)
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
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">{{ number_format($item->discount_percentage ?? 0, 1) }}%</td>
                        <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- TOTALS - RIGHT ALIGNED -->
        <div class="totals-wrapper">
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal:</td>
                        <td class="total-amount">{{ $purchaseOrder->currency ?? 'AED' }} {{ number_format($purchaseOrder->sub_total, 2) }}</td>
                    </tr>
                    @if($purchaseOrder->tax_amount > 0)
                    <tr>
                        <td class="total-label">Tax:</td>
                        <td class="total-amount">{{ $purchaseOrder->currency ?? 'AED' }} {{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($purchaseOrder->shipping_cost > 0)
                    <tr>
                        <td class="total-label">Shipping:</td>
                        <td class="total-amount">{{ $purchaseOrder->currency ?? 'AED' }} {{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="grand-total">
                        <td class="total-label">Total Amount:</td>
                        <td class="total-amount">{{ $purchaseOrder->currency ?? 'AED' }} {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @else
        <div class="items-section">
            <table class="items-table">
                <tbody>
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px; color: var(--text-muted); font-style: italic;">
                            No items added to this purchase order
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- TERMS & CONDITIONS -->
        @if(!empty(trim($purchaseOrder->terms_conditions)))
        <div class="content-section">
            <div class="content-title">Terms & Conditions</div>
            <div class="content-text">{{ $purchaseOrder->terms_conditions }}</div>
        </div>
        @endif

        <!-- NOTES -->
        @if($purchaseOrder->notes)
        <div class="content-section">
            <div class="content-title">Notes</div>
            <div class="content-text">{{ $purchaseOrder->notes }}</div>
        </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <p><strong>Purchase Order Generated:</strong> {{ nowDubai('d M Y \a\t H:i') }}</p>
            <p>Purchase Order ID: {{ $purchaseOrder->po_number }}</p>
            @if($purchaseOrder->created_by)
                <p>Created by: {{ $purchaseOrder->creator->name ?? 'System' }}</p>
            @endif
        </div>
    </div>
</body>
</html> 