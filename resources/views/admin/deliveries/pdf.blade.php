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

        .document-info {
            width: 38%;
            text-align: right;
        }

        .document-title {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .document-details {
            background-color: var(--light-gray);
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            padding: 15px;
        }

        .document-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .document-details td {
            padding: 4px 0;
            font-size: 9px;
        }

        .document-details .label {
            font-weight: 600;
            color: var(--text-secondary);
            width: 45%;
        }

        .document-details .value {
            color: var(--text-primary);
            font-weight: 500;
        }

        /* ADDRESSES SECTION */
        .addresses-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 30px;
        }

        .address-block {
            width: 48%;
            background-color: var(--light-gray);
            border: 1px solid var(--medium-gray);
            border-radius: 6px;
            padding: 15px;
        }

        .address-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--medium-gray);
        }

        .address-content {
            font-size: 9px;
            line-height: 1.5;
            color: var(--text-secondary);
        }

        /* DELIVERY ITEMS TABLE */
        .items-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--primary-color);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }

        .items-table th {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid var(--medium-gray);
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: var(--light-gray);
        }

        .items-table tbody tr:hover {
            background-color: var(--primary-light);
        }

        /* DELIVERY STATUS */
        .delivery-status {
            background-color: #f0fdf4;
            border: 1px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }

        .status-title {
            color: #166534;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .status-details {
            color: #166534;
            font-size: 10px;
            line-height: 1.5;
        }

        /* SIGNATURE SECTION */
        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--medium-gray);
        }

        .signature-blocks {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }

        .signature-block {
            width: 48%;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid var(--text-primary);
            height: 60px;
            margin-bottom: 10px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .signature-label {
            font-size: 9px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* FOOTER */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--medium-gray);
            text-align: center;
            font-size: 8px;
            color: var(--text-muted);
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
                        <img src="{{ public_path('Images/logo.png') }}" alt="MaxMed UAE">
                    </div>
                    <div class="company-name">MaxMed UAE</div>
                    <div class="company-details">
                        Laboratory Equipment & Medical Supplies<br>
                        📍 Dubai, United Arab Emirates<br>
                        📞 +971 55 460 2500<br>
                        📧 sales@maxmedme.com<br>
                        🌐 www.maxmedme.com
                    </div>
                </div>
                
                <div class="document-info">
                    <div class="document-title">DELIVERY NOTE</div>
                    <div class="document-details">
                        <table>
                            <tr>
                                <td class="label">Delivery Number:</td>
                                <td class="value font-bold">{{ $delivery->delivery_number }}</td>
                            </tr>
                            @if($delivery->tracking_number)
                            <tr>
                                <td class="label">Tracking Number:</td>
                                <td class="value">{{ $delivery->tracking_number }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="label">Delivery Date:</td>
                                <td class="value">{{ formatDubaiDate($delivery->delivered_at ?? $delivery->created_at, 'd M Y') }}</td>
                            </tr>
                            @if($delivery->order)
                            <tr>
                                <td class="label">Order Number:</td>
                                <td class="value">{{ $delivery->order->order_number }}</td>
                            </tr>
                            @endif
                            @if($delivery->carrier)
                            <tr>
                                <td class="label">Carrier:</td>
                                <td class="value">{{ $delivery->carrier }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELIVERY STATUS -->
        @if($delivery->status === 'delivered' && $delivery->signed_at)
        <div class="delivery-status">
            <div class="status-title">✅ DELIVERY COMPLETED</div>
            <div class="status-details">
                This order was successfully delivered and signed for on {{ formatDubaiDate($delivery->delivered_at, 'd M Y \a\t g:i A') }}
                @if($delivery->delivery_conditions && is_array($delivery->delivery_conditions))
                <br>Delivery Conditions: {{ implode(', ', $delivery->delivery_conditions) }}
                @endif
            </div>
        </div>
        @endif

        <!-- ADDRESSES SECTION -->
        <div class="addresses-section">
            <div class="address-block">
                <div class="address-title">DELIVERY ADDRESS</div>
                <div class="address-content">
                    @if($delivery->order && $delivery->order->user)
                        <strong>{{ $delivery->order->customer_name ?? $delivery->order->user->name }}</strong><br>
                        @if($customer && $customer->company_name)
                            {{ $customer->company_name }}<br>
                        @endif
                    @endif
                    {{ $delivery->shipping_address ?? 'Address not specified' }}
                </div>
            </div>
            
            <div class="address-block">
                <div class="address-title">SUPPLIER DETAILS</div>
                <div class="address-content">
                    <strong>MaxMed UAE</strong><br>
                    Laboratory Equipment & Medical Supplies<br>
                    Dubai, United Arab Emirates<br>
                    Phone: +971 55 460 2500<br>
                    Email: sales@maxmedme.com
                </div>
            </div>
        </div>

        <!-- DELIVERED ITEMS -->
        @if($delivery->order && $delivery->order->items && count($delivery->order->items) > 0)
        <div class="items-section">
            <div class="section-title">DELIVERED ITEMS</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">#</th>
                        <th style="width: 50%;">Item Description</th>
                        <th style="width: 12%; text-align: center;">Quantity</th>
                        <th style="width: 15%; text-align: center;">Unit</th>
                        <th style="width: 15%; text-align: right;">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delivery->order->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                            @if($item->product && $item->product->model_number)
                                <br><small class="text-muted">Model: {{ $item->product->model_number }}</small>
                            @endif
                            @if($item->product && $item->product->brand)
                                <br><small class="text-muted">Brand: {{ $item->product->brand }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($item->quantity, 0) }}</td>
                        <td class="text-center">{{ $item->product->unit ?? 'pcs' }}</td>
                        <td class="text-right">
                            @if($item->notes)
                                {{ $item->notes }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- DELIVERY NOTES -->
        @if($delivery->notes)
        <div class="items-section">
            <div class="section-title">DELIVERY NOTES</div>
            <div style="background-color: var(--light-gray); padding: 15px; border-radius: 6px; font-size: 9px; line-height: 1.5;">
                {{ $delivery->notes }}
            </div>
        </div>
        @endif

        <!-- SIGNATURE SECTION -->
        <div class="signature-section">
            <div class="section-title">DELIVERY CONFIRMATION</div>
            <div class="signature-blocks">
                <div class="signature-block">
                    <div class="signature-line">
                        @if($delivery->signed_at)
                            <span style="color: var(--success-color); font-weight: bold;">✓ SIGNED</span>
                        @endif
                    </div>
                    <div class="signature-label">Customer Signature</div>
                    @if($delivery->signed_at)
                        <div style="font-size: 8px; color: var(--text-muted); margin-top: 5px;">
                            Signed: {{ formatDubaiDate($delivery->signed_at, 'd M Y \a\t g:i A') }}
                        </div>
                    @endif
                </div>
                
                <div class="signature-block">
                    <div class="signature-line">
                        <span style="color: var(--success-color); font-weight: bold;">MaxMed UAE</span>
                    </div>
                    <div class="signature-label">Authorized Representative</div>
                    <div style="font-size: 8px; color: var(--text-muted); margin-top: 5px;">
                        Date: {{ formatDubaiDate($delivery->created_at, 'd M Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>This delivery note was generated electronically by MaxMed UAE system.</p>
            <p>For any inquiries regarding this delivery, please contact us at +971 55 460 2500 or sales@maxmedme.com</p>
            <p style="margin-top: 10px;">Thank you for choosing MaxMed UAE - Your trusted laboratory equipment partner</p>
        </div>
    </div>
</body>
</html>
