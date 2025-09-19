<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order {{ $purchaseOrder->po_number }}</title>
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

        .supplier-section {
            width: 60%;
        }

        .supplier-info {
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

        .supplier-name {
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

        /* STATUS BADGES */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .status-draft { background-color: #f3f4f6; color: #6b7280; }
        .status-sent_to_supplier { background-color: var(--primary-light); color: var(--primary-color); }
        .status-acknowledged { background-color: #fef3c7; color: var(--warning-color); }
        .status-in_production { background-color: #e9d5ff; color: #7c3aed; }
        .status-completed { background-color: #d1fae5; color: var(--success-color); }
        .status-pending { background-color: #fef2f2; color: var(--danger-color); }
        .status-partial { background-color: #fef3c7; color: var(--warning-color); }
        .status-paid { background-color: #d1fae5; color: var(--success-color); }

        /* DESCRIPTION SECTION */
        .description-section {
            margin-bottom: 25px;
            background-color: var(--primary-light);
            padding: 15px;
            border-radius: 6px;
            border-left: 3px solid var(--primary-color);
        }

        .description-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--primary-color);
            margin-bottom: 6px;
            font-weight: 700;
        }

        .description-content {
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

        .item-brand {
            font-size: 9px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .item-specs {
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

        /* FOOTER */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            color: var(--text-muted);
            font-size: 9px;
        }

        /* STAMP */
        .stamp {
            position: fixed;
            right: 35px; /* align to the right side, near totals box */
            bottom: 110px; /* a little lower than totals box */
            width: 180px;
            opacity: 0.9;
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
                    <div class="document-title">Purchase Order</div>
                    <div class="document-number">{{ $purchaseOrder->po_number }}</div>
                </div>
            </div>
        </div>

        <!-- SUPPLIER INFO & META DATA -->
        <div class="meta-wrapper">
            <div class="supplier-section">
                <div class="supplier-info">
                    <div class="section-heading">Supplier Information</div>
                    <div class="supplier-name">{{ $purchaseOrder->supplier_name }}</div>
                    @if($purchaseOrder->supplier_email || $purchaseOrder->supplier_phone || $purchaseOrder->supplier_address)
                        <div style="font-size: 10px; color: var(--text-secondary); margin-top: 6px; line-height: 1.5;">
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
                    @if($purchaseOrder->payment_terms)
                    <tr>
                        <td class="label">Payment Terms:</td>
                        <td class="value">{{ $purchaseOrder->payment_terms }}</td>
                    </tr>
                    @endif
                    @if($purchaseOrder->shipping_method)
                    <tr>
                        <td class="label">Shipping Method:</td>
                        <td class="value">{{ $purchaseOrder->shipping_method }}</td>
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

        @php
            // Prefer explicit PO currency if available (supports AED, USD, CNY, HKD)
            $poCurrency = strtoupper($purchaseOrder->currency ?? '');
            if (in_array($poCurrency, ['AED', 'USD', 'CNY', 'HKD'])) {
                $displayCurrency = $poCurrency;
            } else {
                // Fallback: derive from item price_type prevalence
                $hasUsdItems = $purchaseOrder->items->where('price_type', 'usd')->count() > 0;
                $hasAedItems = $purchaseOrder->items->where('price_type', 'aed')->count() > 0;
                $displayCurrency = 'AED';
                if ($hasUsdItems && !$hasAedItems) {
                    $displayCurrency = 'USD';
                } elseif ($hasUsdItems && $hasAedItems) {
                    $usdCount = $purchaseOrder->items->where('price_type', 'usd')->count();
                    $aedCount = $purchaseOrder->items->where('price_type', 'aed')->count();
                    $displayCurrency = $usdCount > $aedCount ? 'USD' : 'AED';
                }
            }
        @endphp

        <!-- ITEMS TABLE -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Item Description</th>
                        <th style="width: 20%;">Specifications</th>
                        <th style="width: 10%;" class="text-right">Qty</th>
                        <th style="width: 15%;" class="text-right">Rate ({{ $displayCurrency }})</th>
                        <th style="width: 10%;" class="text-right">Discount</th>
                        <th style="width: 15%;" class="text-right">Amount ({{ $displayCurrency }})</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchaseOrder->items as $index => $item)
                    <tr>
                        <td class="item-description">
                            @if($item->product)
                                {{ $item->product->name }}
                                <div style="font-size: 9px; margin-top: 3px;">
                                    <a href="{{ route('product.show', $item->product) }}" style="color: #0ea5e9; text-decoration: none;">View product page</a>
                                    @if($item->product->pdf_file)
                                        <span style="color: #6b7280; margin: 0 5px;">|</span>
                                        <a href="{{ Storage::url($item->product->pdf_file) }}" target="_blank" style="color: #dc2626; text-decoration: none;">
                                            <i class="fas fa-file-pdf" style="margin-right: 2px;"></i>Product PDF
                                        </a>
                                    @endif
                                </div>
                                @if($item->product->brand)
                                    <div class="item-brand">
                                        <span style="font-weight: 600;">Brand:</span> {{ $item->product->brand->name }}
                                    </div>
                                @endif
                            @else
                                {{ $item->item_description }}
                            @endif
                        </td>
                        <td class="item-specs">
                            @php
                                $hasSpecifications = $item->specifications && !empty(trim($item->specifications));
                                $hasSize = $item->size && !empty(trim($item->size));
                                $selectedSpecs = [];
                                
                                if ($hasSpecifications) {
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
                                }
                            @endphp
                            
                            @if($hasSpecifications && count($selectedSpecs) > 0)
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                    <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 2px;">Specifications:</div>
                                    @foreach($selectedSpecs as $spec)
                                        <div style="margin-bottom: 1px;">• {{ $spec }}</div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($hasSize)
                                @if($hasSpecifications && count($selectedSpecs) > 0)
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3; margin-top: 8px;">
                                        <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 1px;">Size:</div>
                                        <div>{{ $item->size }}</div>
                                    </div>
                                @else
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                        <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 1px;">Size:</div>
                                        <div>{{ $item->size }}</div>
                                    </div>
                                @endif
                            @endif
                            
                            @if(!$hasSpecifications && !$hasSize)
                                <span style="font-size: 9px; color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->discount_percentage ?? 0, 1) }}%</td>
                        <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 30px; color: var(--text-muted); font-style: italic;">
                            No items added to this purchase order
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
                            <td class="total-amount">{{ $displayCurrency }} {{ number_format($purchaseOrder->sub_total, 2) }}</td>
                        </tr>
                        @if($purchaseOrder->tax_amount > 0)
                        <tr>
                            <td class="total-label">Tax:</td>
                            <td class="total-amount">{{ $displayCurrency }} {{ number_format($purchaseOrder->tax_amount, 2) }}</td>
                        </tr>
                        @endif
                        @if($purchaseOrder->shipping_cost > 0)
                        <tr>
                            <td class="total-label">Shipping:</td>
                            <td class="total-amount">{{ $displayCurrency }} {{ number_format($purchaseOrder->shipping_cost, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td class="total-label">Total Amount:</td>
                            <td class="total-amount">{{ $displayCurrency }} {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- NET ORDER VALUE IN WORDS -->
        <div class="content-section" style="margin-bottom: 15px; background-color: white; border-left: 3px solid var(--primary-color);">
            @php
                // Avoid global function name conflicts
                if (!function_exists('po_number_to_words')) {
                function po_number_to_words($number) {
                    $ones = array(
                        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
                        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
                        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
                        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
                    );
                    
                    $tens = array(
                        0 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
                        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
                    );
                    
                    if ($number < 20) {
                        return $ones[$number];
                    } elseif ($number < 100) {
                        return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
                    } elseif ($number < 1000) {
                        return $ones[intval($number / 100)] . ' Hundred ' . po_number_to_words($number % 100);
                    } elseif ($number < 1000000) {
                        return po_number_to_words(intval($number / 1000)) . ' Thousand ' . po_number_to_words($number % 1000);
                    } elseif ($number < 1000000000) {
                        return po_number_to_words(intval($number / 1000000)) . ' Million ' . po_number_to_words($number % 1000000);
                    }
                    return 'Number too large';
                }
                }
                
                $amount = $purchaseOrder->total_amount;
                $wholePart = floor($amount);
                $decimalPart = round(($amount - $wholePart) * 100);
                
                $amountInWords = po_number_to_words($wholePart);
                if ($decimalPart > 0) {
                    $amountInWords .= ' and ' . po_number_to_words($decimalPart) . ' Fils';
                }
                $amountInWords = trim($amountInWords);
                
                $currencyName = 'UAE Dirhams';
                if ($displayCurrency === 'USD') { $currencyName = 'US Dollars'; }
                elseif ($displayCurrency === 'CNY') { $currencyName = 'Chinese Yuan'; }
                elseif ($displayCurrency === 'HKD') { $currencyName = 'Hong Kong Dollars'; }
                $finalAmountInWords = $amountInWords . ' ' . $currencyName . ' Only';
            @endphp
            
            <div>
                <div class="content-title" style="color: var(--primary-color);">Net Order Value (in Words)</div>
                <div style="font-size: 10px; color: var(--text-primary); font-weight: 600; line-height: 1.4;">
                    {{ $finalAmountInWords }}
                </div>
            </div>
        </div>

        <!-- COMPANY STAMP (always render if file exists) -->
        @php 
            $stampPathUpper = public_path('Images/stamp.png');
            $stampPathLower = public_path('images/stamp.png');
            $resolvedStamp = file_exists($stampPathUpper) ? $stampPathUpper : (file_exists($stampPathLower) ? $stampPathLower : null);
        @endphp
        @if($resolvedStamp)
            <img class="stamp" src="{{ $resolvedStamp }}" alt="Company Stamp">
        @endif


        <!-- TERMS & CONDITIONS -->
        @if(!empty(trim($purchaseOrder->terms_conditions)))
        <div class="content-section">
            <div class="content-title">Terms & Conditions</div>
            <div class="content-text">{{ $purchaseOrder->terms_conditions }}</div>
        </div>
        @endif

        <!-- SPECIAL INSTRUCTIONS -->
        @if(!empty(trim($purchaseOrder->special_instructions)))
        <div class="content-section">
            <div class="content-title">Special Instructions</div>
            <div class="content-text">{{ $purchaseOrder->special_instructions }}</div>
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