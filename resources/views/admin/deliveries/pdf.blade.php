<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Note {{ $delivery->delivery_number }}</title>
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

        /* ITEMS TABLE */
        .items-section {
            margin-bottom: 25px;
        }

        /* ORDER DETAILS SECTION */
        .order-details-section {
            margin-bottom: 25px;
        }

        /* ORDER TOTALS SECTION */
        .order-totals-section {
            margin-top: 25px;
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
            background-color: var(--primary-light);
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

        .item-specs {
            font-size: 9px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        .item-name {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
            line-height: 1.3;
        }

        .item-details {
            color: var(--text-muted);
            font-size: 8px;
            line-height: 1.4;
        }

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

        /* SIGNATURE SECTION */
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-color);
        }

        .signature-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 40px;
            margin-top: 30px;
        }

        .signature-block {
            width: 48%;
        }

        .signature-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
            margin-bottom: 15px;
            font-weight: 700;
        }

        .signature-box {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light-gray);
            margin-bottom: 10px;
        }

        .signature-image {
            max-width: 100%;
            max-height: 70px;
            border-radius: 4px;
        }

        .signature-info {
            text-align: center;
            margin-top: 10px;
        }

        .signature-name {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 3px;
        }

        .signature-date {
            font-size: 8px;
            color: var(--text-muted);
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid var(--primary-color);
            text-align: center;
            font-size: 9px;
            color: var(--text-secondary);
            background-color: var(--light-gray);
            padding: 20px;
            border-radius: 8px;
        }

        .footer p {
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .footer p:last-child {
            margin-bottom: 0;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* UTILITIES */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .text-primary { color: var(--text-primary); }
        .text-secondary { color: var(--text-secondary); }
        .text-muted { color: var(--text-muted); }
        .text-success { color: var(--success-color); }
        .text-warning { color: var(--warning-color); }
        .text-danger { color: var(--danger-color); }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- HEADER SECTION -->
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
                    <div class="document-title">DELIVERY NOTE</div>
                    <div class="document-number">{{ $delivery->delivery_number }}</div>
                </div>
            </div>
        </div>

        <!-- ORDER DETAILS SECTION -->
        @if($delivery->order)
        <div class="order-details-section" style="margin-bottom: 25px;">
            <div style="background-color: var(--primary-light); padding: 20px; border-radius: 8px; border-left: 4px solid var(--primary-color); margin-bottom: 20px;">
                <div class="section-heading" style="color: var(--primary-color); margin-bottom: 15px;">Order Information</div>
                <div style="display: flex; justify-content: space-between; gap: 30px;">
                    <div style="flex: 1;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 10px;">
                            <div>
                                <span style="font-weight: 600; color: var(--text-secondary);">Order Number:</span>
                                <span style="color: var(--text-primary); font-weight: 600;">{{ $delivery->order->order_number }}</span>
                            </div>
                            <div>
                                <span style="font-weight: 600; color: var(--text-secondary);">Order Date:</span>
                                <span style="color: var(--text-primary);">{{ formatDubaiDate($delivery->order->created_at, 'd M Y') }}</span>
                            </div>
                            <div>
                                <span style="font-weight: 600; color: var(--text-secondary);">Currency:</span>
                                <span style="color: var(--text-primary);">{{ $delivery->order->currency ?? 'AED' }}</span>
                            </div>
                            <div>
                                <span style="font-weight: 600; color: var(--text-secondary);">Order Status:</span>
                                <span style="color: var(--text-primary); text-transform: capitalize;">{{ $delivery->order->status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- META INFORMATION -->
        <div class="meta-wrapper">
            <div class="client-section">
                <div class="client-info">
                    <div class="section-heading">Delivery Address</div>
                    @if($delivery->order && $delivery->order->user)
                        <div class="client-name">{{ $delivery->order->customer_name ?? $delivery->order->user->name }}</div>
                        @if($customer && $customer->company_name)
                            <div class="client-address">{{ $customer->company_name }}</div>
                        @endif
                        @if($delivery->order->user && $delivery->order->user->email)
                            <div class="client-address" style="margin-top: 4px; color: var(--accent-color);">{{ $delivery->order->user->email }}</div>
                        @endif
                    @endif
                    <div class="client-address">{{ $delivery->shipping_address ?? 'Address not specified' }}</div>
                </div>
            </div>
            
            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Delivery Date</td>
                        <td class="value">{{ formatDubaiDate($delivery->delivered_at ?? $delivery->created_at, 'd M Y') }}</td>
                    </tr>
                    @if($delivery->tracking_number)
                    <tr>
                        <td class="label">Tracking Number</td>
                        <td class="value">{{ $delivery->tracking_number }}</td>
                    </tr>
                    @endif
                    @if($delivery->order)
                    <tr>
                        <td class="label">Order Number</td>
                        <td class="value">{{ $delivery->order->order_number }}</td>
                    </tr>
                    @endif
                    @if($delivery->carrier && $delivery->carrier !== 'TBD')
                    <tr>
                        <td class="label">Carrier</td>
                        <td class="value">{{ $delivery->carrier }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label">Status</td>
                        <td class="value">{{ ucfirst($delivery->status) }}</td>
                    </tr>
                    @if($delivery->order && $delivery->order->notes && !str_contains(strtolower($delivery->order->notes), 'auto-created from proforma invoice') && !str_contains(strtolower($delivery->order->notes), 'payment on delivery'))
                    <tr>
                        <td class="label">Order Notes</td>
                        <td class="value" style="font-size: 9px; max-width: 200px;">{{ Str::limit($delivery->order->notes, 100) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        @if($delivery->order && $delivery->order->items && count($delivery->order->items) > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Product Code</th>
                        <th style="width: 25%;">Item Description</th>
                        <th style="width: 18%;" class="text-center">Specifications</th>
                        <th style="width: 10%;" class="text-center">Qty</th>
                        <th style="width: 12%;" class="text-right">Rate ({{ $delivery->order->currency ?? 'AED' }})</th>
                        <th style="width: 8%;" class="text-center">Disc.</th>
                        <th style="width: 15%;" class="text-right">Total ({{ $delivery->order->currency ?? 'AED' }})</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delivery->order->items as $index => $item)
                    <tr>
                        <td>
                            @if($item->product)
                                <div style="font-size: 10px; font-weight: 600; color: var(--text-primary);">
                                    {{ $item->product->sku ?? $item->product->product_code ?? 'N/A' }}
                                </div>
                                @if($item->product->model_number)
                                    <div style="font-size: 8px; color: var(--text-muted); margin-top: 2px;">
                                        Model: {{ $item->product->model_number }}
                                    </div>
                                @endif
                            @else
                                <div style="font-size: 10px; color: var(--text-muted);">N/A</div>
                            @endif
                        </td>
                        <td>
                            <div class="item-description">
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 3px;">
                                    {{ $item->description ?? ($item->product ? $item->product->name : 'Product #' . $item->product_id) }}
                                </div>
                                @if($item->product && $item->product->brand)
                                    <div style="font-size: 9px; color: var(--text-secondary); margin-bottom: 2px;">
                                        <span style="font-weight: 600;">Brand:</span> {{ $item->product->brand->name }}
                                    </div>
                                @endif
                                @if($item->variation)
                                    <div style="font-size: 9px; color: var(--text-secondary); margin-bottom: 2px;">
                                        <span style="font-weight: 600;">Variation:</span> {{ $item->variation }}
                                    </div>
                                @endif
                                @if($item->product)
                                    <div style="font-size: 8px; margin-top: 3px; color: var(--accent-color);">
                                        <span style="color: #6b7280;">Product ID:</span> {{ $item->product->id }}
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if($item->product && $item->product->specifications)
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                    @php
                                        $productSpecs = [];
                                        try {
                                            if (is_string($item->product->specifications)) {
                                                $productSpecs = json_decode($item->product->specifications, true) ?? [$item->product->specifications];
                                            }
                                        } catch (Exception $e) {
                                            $productSpecs = [$item->product->specifications];
                                        }
                                    @endphp
                                    @foreach(array_slice($productSpecs, 0, 3) as $spec)
                                        <div style="margin-bottom: 2px;">{{ Str::limit($spec, 20) }}</div>
                                    @endforeach
                                    @if(count($productSpecs) > 3)
                                        <div style="color: var(--accent-color); font-size: 8px;">+{{ count($productSpecs) - 3 }} more</div>
                                    @endif
                                </div>
                            @elseif($item->specifications && !empty(trim($item->specifications)))
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                    {{ Str::limit($item->specifications, 50) }}
                                </div>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div style="font-weight: 600; color: var(--text-primary);">
                                {{ number_format($item->quantity, 0) }}
                            </div>
                            @if($item->product && $item->product->unit_of_measure)
                                <div style="font-size: 8px; color: var(--text-muted);">
                                    {{ $item->product->unit_of_measure }}
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <div style="font-weight: 600; color: var(--text-primary);">
                                {{ number_format($item->price ?? 0, 2) }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if(($item->discount_percentage ?? 0) > 0)
                                <div style="font-weight: 600; color: var(--warning-color);">
                                    {{ number_format($item->discount_percentage, 1) }}%
                                </div>
                            @elseif(($item->discount_amount ?? 0) > 0)
                                <div style="font-weight: 600; color: var(--warning-color);">
                                    {{ number_format($item->discount_amount, 2) }}
                                </div>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @php
                                $unitPrice = $item->price ?? 0;
                                $quantity = $item->quantity;
                                $discountPercentage = $item->discount_percentage ?? 0;
                                $discountAmount = $item->discount_amount ?? 0;
                                $subtotal = $unitPrice * $quantity;
                                
                                // Calculate discount
                                $calculatedDiscount = 0;
                                if ($discountAmount > 0) {
                                    $calculatedDiscount = $discountAmount;
                                } elseif ($discountPercentage > 0) {
                                    $calculatedDiscount = $subtotal * ($discountPercentage / 100);
                                }
                                
                                $totalAfterDiscount = $subtotal - $calculatedDiscount;
                            @endphp
                            <div style="font-weight: 700; color: var(--text-primary);">
                                {{ number_format($totalAfterDiscount, 2) }}
                            </div>
                            @if($calculatedDiscount > 0)
                                <div style="font-size: 8px; color: var(--text-muted); text-decoration: line-through;">
                                    {{ number_format($subtotal, 2) }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- ORDER TOTALS SECTION -->
        @if($delivery->order)
        <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
            <div style="width: 300px;">
                <table style="width: 100%; border-collapse: collapse; background-color: var(--light-gray); border-radius: 6px; overflow: hidden;">
                    @php
                        $subtotal = 0;
                        $totalDiscount = 0;
                        $totalTax = 0;
                        $shippingCost = $delivery->order->shipping_rate ?? 0;
                        
                        foreach($delivery->order->items as $item) {
                            $itemSubtotal = ($item->price ?? 0) * $item->quantity;
                            $subtotal += $itemSubtotal;
                            
                            // Calculate item discount
                            $itemDiscount = 0;
                            if (($item->discount_amount ?? 0) > 0) {
                                $itemDiscount = $item->discount_amount;
                            } elseif (($item->discount_percentage ?? 0) > 0) {
                                $itemDiscount = $itemSubtotal * ($item->discount_percentage / 100);
                            }
                            $totalDiscount += $itemDiscount;
                        }
                        
                        $netAmount = $subtotal - $totalDiscount;
                        
                        // Calculate VAT if applicable
                        if (($delivery->order->vat_rate ?? 0) > 0) {
                            $totalTax = $netAmount * ($delivery->order->vat_rate / 100);
                        }
                        
                        $finalTotal = $netAmount + $totalTax + $shippingCost;
                    @endphp
                    
                    <tr style="background-color: var(--medium-gray);">
                        <td style="padding: 12px 15px; font-weight: 600; color: var(--text-secondary); font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">Subtotal</td>
                        <td style="padding: 12px 15px; text-align: right; font-weight: 600; color: var(--text-primary);">{{ number_format($subtotal, 2) }} {{ $delivery->order->currency ?? 'AED' }}</td>
                    </tr>
                    
                    @if($totalDiscount > 0)
                    <tr>
                        <td style="padding: 10px 15px; font-weight: 600; color: var(--warning-color); font-size: 10px;">Total Discount</td>
                        <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: var(--warning-color);">-{{ number_format($totalDiscount, 2) }} {{ $delivery->order->currency ?? 'AED' }}</td>
                    </tr>
                    @endif
                    
                    @if($shippingCost > 0)
                    <tr>
                        <td style="padding: 10px 15px; font-weight: 600; color: var(--text-secondary); font-size: 10px;">Shipping</td>
                        <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: var(--text-primary);">{{ number_format($shippingCost, 2) }} {{ $delivery->order->currency ?? 'AED' }}</td>
                    </tr>
                    @endif
                    
                    @if($totalTax > 0)
                    <tr>
                        <td style="padding: 10px 15px; font-weight: 600; color: var(--text-secondary); font-size: 10px;">VAT ({{ $delivery->order->vat_rate }}%)</td>
                        <td style="padding: 10px 15px; text-align: right; font-weight: 600; color: var(--text-primary);">{{ number_format($totalTax, 2) }} {{ $delivery->order->currency ?? 'AED' }}</td>
                    </tr>
                    @endif
                    
                    <tr style="background-color: var(--primary-color); color: white; border-top: 2px solid var(--primary-color);">
                        <td style="padding: 15px; font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Total Amount</td>
                        <td style="padding: 15px; text-align: right; font-weight: 700; font-size: 12px;">{{ number_format($finalTotal, 2) }} {{ $delivery->order->currency ?? 'AED' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif
        @endif

        <!-- DELIVERY NOTES -->
        @if($delivery->notes && !str_contains(strtolower($delivery->notes), 'auto-created from proforma invoice') && !str_contains(strtolower($delivery->notes), 'payment on delivery'))
        <div class="description-section">
            <div class="description-title">Delivery Notes</div>
            <div class="description-content">{{ $delivery->notes }}</div>
        </div>
        @endif

        <!-- SIGNATURE SECTION -->
        @if($delivery->signed_at)
        <div class="signature-section">
            <div class="signature-wrapper">
                <div class="signature-block">
                    <div class="signature-title">Customer Signature</div>
                    <div class="signature-box">
                        @if($delivery->customer_signature)
                            <img src="{{ public_path('storage/' . $delivery->customer_signature) }}" alt="Customer Signature" class="signature-image">
                        @else
                            <span style="color: var(--success-color); font-weight: bold; font-size: 12px;">✓ SIGNED DIGITALLY</span>
                        @endif
                    </div>
                    <div class="signature-info">
                        <div class="signature-date">{{ formatDubaiDate($delivery->signed_at, 'd M Y \a\t g:i A') }}</div>
                        @if($delivery->delivery_conditions && is_array($delivery->delivery_conditions))
                            <div style="font-size: 8px; color: var(--text-muted); margin-top: 3px;">
                                Conditions: {{ implode(', ', $delivery->delivery_conditions) }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="signature-block">
                    <div class="signature-title">Authorized Representative</div>
                    <div class="signature-box">
                        <img src="{{ public_path('Images/stamp.png') }}" alt="MaxMed UAE Stamp" class="signature-image">
                    </div>
                    <div class="signature-info">
                        @if(isset($authorizedUser) && $authorizedUser)
                            <div class="signature-name">{{ $authorizedUser->name }}</div>
                        @elseif(auth()->check())
                            <div class="signature-name">{{ auth()->user()->name }}</div>
                        @else
                            <div class="signature-name">MaxMed Representative</div>
                        @endif
                        <div class="signature-date">{{ formatDubaiDate($delivery->delivered_at ?? $delivery->created_at, 'd M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- FOOTER -->
        <div class="footer">
            <p><strong>MaxMed Scientific and Laboratory Equipment Trading Co. LLC</strong></p>
            <p>Dubai 448945, United Arab Emirates | sales@maxmedme.com | www.maxmedme.com</p>
            <p>This delivery note was generated electronically on {{ formatDubaiDate(now(), 'd M Y \a\t g:i A') }}</p>
            <p>Thank you for choosing MaxMed UAE - Your trusted laboratory equipment partner</p>
        </div>
    </div>
</body>
</html>
