<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice'); ?> <?php echo e($invoice->invoice_number); ?></title>
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

        /* STAMP (match Purchase Order style) */
        .stamp {
            position: fixed;
            right: 35px; /* align to the right side, near totals box */
            bottom: 110px; /* a little lower than totals box */
            width: 180px;
            opacity: 0.9;
            z-index: 1000;
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

        /* SHIPPING ADDRESS SECTION */
        .shipping-section {
            margin-top: 15px;
        }

        .shipping-info {
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 20px;
            border-left: 3px solid var(--accent-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
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

        .item-specs {
            font-size: 9px;
            color: var(--text-muted);
            margin-top: 3px;
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

        /* PAYMENT INFORMATION */
        .payment-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 6px;
            border-left: 3px solid var(--warning-color);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .payment-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #856404;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .payment-text {
            font-size: 10px;
            color: #856404;
            line-height: 1.5;
            margin-bottom: 6px;
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
    <?php if($invoice->type === 'proforma'): ?>
    <?php 
        $stampPathUpper = public_path('Images/stamp.png');
        $stampPathLower = public_path('images/stamp.png');
        $resolvedStamp = file_exists($stampPathUpper) ? $stampPathUpper : (file_exists($stampPathLower) ? $stampPathLower : null);
    ?>
    <?php if($resolvedStamp): ?>
        <img class="stamp" src="<?php echo e($resolvedStamp); ?>" alt="Company Stamp">
    <?php endif; ?>
    <?php endif; ?>
    <?php if($invoice->type === 'final'): ?>
    <?php 
        $paidStampUpper = public_path('Images/paid.png');
        $paidStampLower = public_path('images/paid.png');
        $paidStamp = file_exists($paidStampUpper) ? $paidStampUpper : (file_exists($paidStampLower) ? $paidStampLower : null);
        $isPaidFinal = (float)($invoice->total_amount ?? 0) - (float)($invoice->paid_amount ?? 0) <= 0.0001;
    ?>
    <?php if($paidStamp && $isPaidFinal): ?>
        <img class="stamp" src="<?php echo e($paidStamp); ?>" alt="Paid Stamp">
    <?php endif; ?>
    <?php endif; ?>
    <div class="document-container">
        <!-- HEADER WITH COMPANY INFO & DOCUMENT TITLE -->
        <div class="header-wrapper">
            <div class="header-section">
                <div class="company-section">
                    <div class="company-logo">
                        <img src="<?php echo e(public_path('Images/logo.png')); ?>" alt="MaxMed Logo">
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
                    <div class="document-title"><?php echo e($invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice'); ?></div>
                    <div class="document-number"><?php echo e($invoice->invoice_number); ?></div>
                </div>
            </div>
        </div>

        <!-- CLIENT INFO & META DATA -->
        <div class="meta-wrapper">
            <div class="client-section">
                <div class="client-info">
                    <div class="section-heading">Bill To</div>
                    <div class="client-name">
                        <?php echo e($invoice->customer_name); ?>

                        <?php if($customer && $customer->company_name): ?>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 3px; font-weight: 500;">
                                <?php echo e($customer->company_name); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($customer): ?>
                        <div class="client-address">
                            <?php if($customer->email): ?>
                                <div><strong>Email:</strong> <?php echo e($customer->email); ?></div>
                            <?php endif; ?>
                            <?php if($customer->phone): ?>
                                <div><strong>Phone:</strong> <?php echo e($customer->phone); ?></div>
                            <?php endif; ?>
                            <?php if($customer->tax_id): ?>
                                <div><strong>Tax ID:</strong> <?php echo e($customer->tax_id); ?></div>
                            <?php endif; ?>
                            <?php $billing = $customer->billing_address; ?>
                            <?php if(!empty($billing)): ?>
                                <div style="margin-top: 6px;"><strong>Billing Address:</strong><br><?php echo nl2br(e($billing)); ?></div>
                            <?php endif; ?>
                            <?php $shipping = $customer->shipping_address; ?>
                            <?php if(!empty($shipping) && $shipping !== $billing): ?>
                                <div style="margin-top: 6px;"><strong>Shipping Address:</strong><br><?php echo nl2br(e($shipping)); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if($invoice->shipping_address && $invoice->shipping_address !== $invoice->billing_address): ?>
                <div class="shipping-section">
                    <div class="shipping-info">
                        <div class="section-heading">Ship To</div>
                        <div class="client-address"><?php echo e(nl2br($invoice->shipping_address)); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Invoice Date:</td>
                        <td class="value"><?php echo e(formatDubaiDate($invoice->invoice_date, 'd M Y')); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Due Date:</td>
                        <td class="value"><?php echo e(formatDubaiDate($invoice->due_date, 'd M Y')); ?></td>
                    </tr>
                    <tr>
                        <td class="label">Payment Terms:</td>
                        <td class="value"><?php echo e($invoice::PAYMENT_TERMS[$invoice->payment_terms] ?? ucfirst($invoice->payment_terms)); ?></td>
                    </tr>
                    <?php if($invoice->reference_number): ?>
                    <tr>
                        <td class="label">Reference:</td>
                        <td class="value"><?php echo e($invoice->reference_number); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($invoice->po_number): ?>
                    <tr>
                        <td class="label">PO Number:</td>
                        <td class="value"><?php echo e($invoice->po_number); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($invoice->quote): ?>
                    <tr>
                        <td class="label">Quote Number:</td>
                        <td class="value"><?php echo e($invoice->quote->quote_number); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- DESCRIPTION SECTION -->
        <?php if($invoice->description): ?>
        <div class="description-section">
            <div class="description-title">Description</div>
            <div class="description-content"><?php echo e($invoice->description); ?></div>
        </div>
        <?php endif; ?>

        <!-- ITEMS TABLE -->
        <?php if($invoice->items->count() > 0): ?>
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Item Description</th>
                        <th style="width: 20%;" class="text-center">Specifications</th>
                        <th style="width: 12%;" class="text-center">Quantity</th>
                        <th style="width: 13%;" class="text-right">Rate (<?php echo e($invoice->currency ?? 'AED'); ?>)</th>
                        <th style="width: 10%;" class="text-center">Discount</th>
                        <th style="width: 20%;" class="text-right">Amount After Discount (<?php echo e($invoice->currency ?? 'AED'); ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $invoice->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="item-description">
                                <?php echo e($item->description); ?>

                                <?php if($item->product): ?>
                                    <div style="font-size: 9px; margin-top: 3px;">
                                        <a href="<?php echo e(route('product.show', $item->product)); ?>" style="color: #0ea5e9; text-decoration: none;">View product page</a>
                                        <?php if($item->product->pdf_file): ?>
                                            <span style="color: #6b7280; margin: 0 5px;">|</span>
                                            <a href="<?php echo e(Storage::url($item->product->pdf_file)); ?>" target="_blank" style="color: #dc2626; text-decoration: none;">
                                                <i class="fas fa-file-pdf" style="margin-right: 2px;"></i>Product PDF
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if($item->product && $item->product->brand): ?>
                                    <div style="font-size: 9px; color: var(--text-secondary); margin-top: 2px;">
                                        <span style="font-weight: 600;">Brand:</span> <?php echo e($item->product->brand->name); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php if($item->specifications && !empty(trim($item->specifications))): ?>
                                <?php
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
                                ?>
                                
                                <?php if(count($selectedSpecs) > 0): ?>
                                    <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3;">
                                        <?php $__currentLoopData = $selectedSpecs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div style="margin-bottom: 2px;"><?php echo e($spec); ?></div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if($item->size && !empty(trim($item->size))): ?>
                                <div style="font-size: 9px; color: var(--text-secondary); line-height: 1.3; margin-top: 3px;">
                                    <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 1px;">Size:</div>
                                    <div><?php echo e($item->size); ?></div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if((!$item->specifications || empty(trim($item->specifications))) && (!$item->size || empty(trim($item->size)))): ?>
                                <span style="color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?php echo e(number_format($item->quantity, 2)); ?></td>
                        <td class="text-right"><?php echo e(number_format($item->unit_price, 2)); ?></td>
                        <td class="text-center">
                            <?php if($item->discount_percentage > 0): ?>
                                <?php echo e(number_format($item->discount_percentage, 2)); ?>%
                                <?php if($item->calculated_discount_amount > 0): ?>
                                    <br><span style="font-size: 8px; color: var(--text-secondary);">(-<?php echo e(number_format($item->calculated_discount_amount, 2)); ?>)</span>
                                <?php endif; ?>
                            <?php elseif($item->calculated_discount_amount > 0): ?>
                                -<?php echo e(number_format($item->calculated_discount_amount, 2)); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?php echo e(number_format($item->line_total, 2)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <!-- TOTALS - RIGHT ALIGNED -->
        <div class="totals-wrapper">
            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="total-label">Subtotal:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->subtotal ?? $invoice->items->sum('line_total'), 2)); ?></td>
                    </tr>
                    <?php if(($invoice->discount_amount ?? 0) > 0): ?>
                    <tr>
                        <td class="total-label">Total Discount:</td>
                        <td class="total-amount">-<?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->discount_amount, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($invoice->shipping_rate > 0): ?>
                    <tr>
                        <td class="total-label">Shipping:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->shipping_rate, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(($invoice->customs_clearance_fee ?? 0) > 0): ?>
                    <tr>
                        <td class="total-label">Customs Clearance:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->customs_clearance_fee, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(($invoice->tax_amount ?? 0) > 0): ?>
                    <tr>
                        <td class="total-label">VAT<?php echo e(($invoice->vat_rate ?? 0) > 0 ? ' (' . number_format($invoice->vat_rate, 1) . '%)' : ''); ?>:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->tax_amount, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="grand-total">
                        <td class="total-label">Total Amount:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->total_amount, 2)); ?></td>
                    </tr>
                    <?php if($invoice->type === 'final'): ?>
                    <?php
                        $paidToDate = (float)($invoice->paid_amount ?? 0);
                        $amountDue = max(0, (float)($invoice->total_amount ?? 0) - $paidToDate);
                    ?>
                    <?php if($paidToDate > 0): ?>
                    <tr>
                        <td class="total-label">Paid to Date:</td>
                        <td class="total-amount"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($paidToDate, 2)); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="total-label">Amount Due:</td>
                        <td class="total-amount">
                            <?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($amountDue, 2)); ?>

                            <?php if($amountDue == 0): ?>
                                <span style="font-size:9px; color: var(--success-color); font-weight:700;">(Paid)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- TOTAL IN WORDS -->
        <div class="total-words-section">
            <div class="total-words-title">Amount in Words:</div>
            <div class="total-words-text"><?php echo e(numberToWords($invoice->total_amount, $invoice->currency ?? 'AED')); ?></div>
        </div>
        <?php endif; ?>

        <!-- PAYMENT INFORMATION -->
        <?php if($invoice->type === 'proforma' && ($invoice->requires_advance_payment || $invoice->payment_terms === 'on_delivery')): ?>
        <div class="payment-section">
            <div class="payment-title">Payment Information</div>
            <?php if($invoice->payment_terms === 'advance_50'): ?>
                <div class="payment-text">This is a proforma invoice requiring <strong>50% advance payment (<?php echo e(number_format($invoice->getAdvanceAmount(), 2)); ?> <?php echo e($invoice->currency); ?>)</strong> before we proceed with your order.</div>
            <?php elseif($invoice->payment_terms === 'advance_100'): ?>
                <div class="payment-text">This is a proforma invoice requiring <strong>full payment (<?php echo e(number_format($invoice->total_amount, 2)); ?> <?php echo e($invoice->currency); ?>)</strong> before we proceed with your order.</div>
            <?php elseif($invoice->payment_terms === 'on_delivery'): ?>
                <div class="payment-text">This is a proforma invoice for <strong>Cash On Delivery</strong>. No advance payment is required.</div>
            <?php elseif($invoice->payment_terms === 'custom' && $invoice->advance_percentage): ?>
                <div class="payment-text">This is a proforma invoice requiring <strong><?php echo e($invoice->advance_percentage); ?>% advance payment (<?php echo e(number_format($invoice->getAdvanceAmount(), 2)); ?> <?php echo e($invoice->currency); ?>)</strong> before we proceed with your order.</div>
            <?php endif; ?>
            <?php if($invoice->payment_terms === 'on_delivery'): ?>
                <div class="payment-text">We will confirm your order and arrange delivery. Cash payment will be collected upon delivery.</div>
            <?php else: ?>
                <div class="payment-text">Once payment is received, we will confirm your order and provide you with a delivery timeline.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if($invoice->type === 'final'): ?>
        <div class="payment-section">
            <div class="payment-title">Final Invoice</div>
            <?php
                // Check delivery status from multiple sources
                $delivery = $invoice->delivery;
                if (!$delivery && $invoice->order && $invoice->order->delivery) {
                    $delivery = $invoice->order->delivery;
                }
                
                // Additional fallback: try to find delivery by customer name and invoice date
                if (!$delivery && $invoice->customer_name) {
                    $relatedDeliveries = \App\Models\Delivery::with('order')
                        ->whereHas('order', function($query) use ($invoice) {
                            $query->whereHas('customer', function($customerQuery) use ($invoice) {
                                $customerQuery->where('name', $invoice->customer_name);
                            });
                        })
                        ->whereDate('created_at', '>=', $invoice->created_at->subDays(30))
                        ->whereDate('created_at', '<=', $invoice->created_at->addDays(30))
                        ->orderBy('created_at', 'desc')
                        ->get();
                    
                    if ($relatedDeliveries->count() > 0) {
                        $delivery = $relatedDeliveries->first();
                    }
                }
                
                $isDelivered = $delivery && in_array($delivery->status, ['delivered']);
                $isShipped = $delivery && in_array($delivery->status, ['in_transit', 'delivered']);
                $parentInvoice = $invoice->parentInvoice;
                $hasAdvancePayment = $parentInvoice && $parentInvoice->paid_amount > 0;
                $isOnDeliveryTerms = $parentInvoice && $parentInvoice->payment_terms === 'on_delivery';
                $paymentCollectedOnDelivery = $isDelivered && $invoice->payment_status === 'paid' && $isOnDeliveryTerms;
                // Determine if a cash receipt exists for the related order
                $hasCashReceipt = false;
                if ($invoice->order && $invoice->order->cashReceipts) {
                    $hasCashReceipt = $invoice->order->cashReceipts->where('status', 'issued')->count() > 0;
                }
                $paidToDateFinal = (float)($invoice->paid_amount ?? 0);
                $amountDueFinal = max(0, (float)($invoice->total_amount ?? 0) - $paidToDateFinal);
                $paymentRecorded = $amountDueFinal == 0;
            ?>

            <?php if($paymentRecorded): ?>
                <div class="payment-text">Payment received in full. No amount due. Thank you for your business.</div>
                <?php if($isDelivered): ?>
                    <div class="payment-text">Order status: Delivered.</div>
                <?php elseif($isShipped): ?>
                    <div class="payment-text">Order status: In transit.</div>
                <?php endif; ?>
                <?php if($invoice->paid_at): ?>
                    <div class="payment-text">Paid on: <?php echo e(formatDubaiDate($invoice->paid_at, 'd M Y')); ?></div>
                <?php endif; ?>
            <?php else: ?>
                <div class="payment-text">Amount Due: <strong><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($amountDueFinal, 2)); ?></strong> <?php if($paidToDateFinal > 0): ?> (Paid to date: <?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($paidToDateFinal, 2)); ?>) <?php endif; ?></div>
                <?php if($isDelivered): ?>
                    <div class="payment-text">Your order has been delivered. Please arrange payment for the balance.</div>
                <?php elseif($isShipped): ?>
                    <div class="payment-text">Your order is in transit. Please arrange payment for the balance.</div>
                <?php else: ?>
                    <div class="payment-text">Your order is being processed for delivery. Please arrange payment for the balance.</div>
                <?php endif; ?>
                <?php if($hasAdvancePayment && !$paymentCollectedOnDelivery): ?>
                    <div class="payment-text">Previous advance payment received: <strong><?php echo e(number_format($parentInvoice->paid_amount, 2)); ?> <?php echo e($invoice->currency ?? 'AED'); ?></strong></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endif; ?>

         <!-- PAYMENT DETAILS (only for final invoices with payments) -->
         <?php if($invoice->type === 'final' && ($invoice->payments && $invoice->payments->count() > 0)): ?>
         <div class="banking-section">
             <div class="banking-title">Payment Details</div>
             <div class="banking-grid">
                 <?php $lastPayment = $invoice->payments->sortByDesc('payment_date')->first(); ?>
                 <div class="banking-item">
                     <div class="banking-label">Paid To Date</div>
                     <div class="banking-value"><?php echo e($invoice->currency ?? 'AED'); ?> <?php echo e(number_format($invoice->paid_amount ?? 0, 2)); ?></div>
                 </div>
                 <?php if($lastPayment): ?>
                 <div class="banking-item">
                     <div class="banking-label">Last Payment Date</div>
                     <div class="banking-value"><?php echo e(formatDubaiDate($lastPayment->payment_date ?? $invoice->paid_at, 'd M Y')); ?></div>
                 </div>
                 <div class="banking-item">
                     <div class="banking-label">Payment Method</div>
                     <div class="banking-value"><?php echo e(\App\Models\Payment::PAYMENT_METHODS[$lastPayment->payment_method] ?? ucfirst(str_replace('_',' ',$lastPayment->payment_method))); ?></div>
                 </div>
                 <?php if($lastPayment->transaction_reference): ?>
                 <div class="banking-item">
                     <div class="banking-label">Transaction Ref</div>
                     <div class="banking-value"><?php echo e($lastPayment->transaction_reference); ?></div>
                 </div>
                 <?php endif; ?>
                 <?php endif; ?>
             </div>
         </div>
         <?php endif; ?>

         <!-- BANKING SECTION -->
         <?php $isPaidInFull = ($invoice->type === 'final') && (float)($invoice->total_amount ?? 0) - (float)($invoice->paid_amount ?? 0) <= 0.0001; ?>
         <?php if(!$isPaidInFull): ?>
         <div class="banking-section">
             <div class="banking-title">Banking Details</div>
             <div class="banking-grid">
                 <div class="banking-item full-width">
                     <div class="banking-label">Account Name</div>
                     <div class="banking-value">MAXMED SCIENTIFIC AND LABORATORY EQUIPMENT TRADING CO. L.L.C</div>
                 </div>
                 <div class="banking-item">
                     <div class="banking-label">Bank Name</div>
                     <div class="banking-value">Mashreq Bank</div>
                 </div>
                 <div class="banking-item">
                     <div class="banking-label">Account Number</div>
                     <div class="banking-value">019101532833</div>
                 </div>
                 <div class="banking-item">
                     <div class="banking-label">IBAN Number</div>
                     <div class="banking-value">AE900330000019101532833</div>
                 </div>
                 <div class="banking-item">
                     <div class="banking-label">SWIFT Code</div>
                     <div class="banking-value">BOMLAEAD</div>
                 </div>
             </div>
         </div>
         <?php endif; ?>

         <!-- TERMS & CONDITIONS -->
         <?php if($invoice->terms_conditions): ?>
        <div class="content-section">
            <div class="content-title">Terms & Conditions</div>
            <div class="content-text"><?php echo e($invoice->terms_conditions); ?></div>
        </div>
        <?php endif; ?>

        <!-- NOTES -->
        <?php if($invoice->notes): ?>
        <div class="content-section">
            <div class="content-title">Notes</div>
            <div class="content-text"><?php echo e($invoice->notes); ?></div>
        </div>
        <?php endif; ?>



        <!-- FOOTER -->
        <div class="footer">
            <p><strong><?php echo e($invoice->type === 'proforma' ? 'Proforma Invoice' : 'Invoice'); ?> Generated:</strong> <?php echo e(nowDubai('d M Y \a\t H:i')); ?></p>
            <p>Invoice ID: <?php echo e($invoice->invoice_number); ?></p>
        </div>
    </div>
</body>
</html> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/admin/invoices/pdf.blade.php ENDPATH**/ ?>