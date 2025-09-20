<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Note <?php echo e($delivery->delivery_number); ?></title>
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
                    <div class="document-title">DELIVERY NOTE</div>
                    <div class="document-number"><?php echo e($delivery->delivery_number); ?></div>
                </div>
            </div>
        </div>

        <!-- META INFORMATION -->
        <div class="meta-wrapper">
            <div class="client-section">
                <div class="client-info">
                    <div class="section-heading">Delivery Address</div>
                    <?php if($delivery->order && $delivery->order->user): ?>
                        <div class="client-name"><?php echo e($delivery->order->customer_name ?? $delivery->order->user->name); ?></div>
                        <?php if($customer && $customer->company_name): ?>
                            <div class="client-address"><?php echo e($customer->company_name); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="client-address"><?php echo e($delivery->shipping_address ?? 'Address not specified'); ?></div>
                </div>
            </div>
            
            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <td class="label">Delivery Date</td>
                        <td class="value"><?php echo e(formatDubaiDate($delivery->delivered_at ?? $delivery->created_at, 'd M Y')); ?></td>
                    </tr>
                    <?php if($delivery->tracking_number): ?>
                    <tr>
                        <td class="label">Tracking Number</td>
                        <td class="value"><?php echo e($delivery->tracking_number); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($delivery->order): ?>
                    <tr>
                        <td class="label">Order Number</td>
                        <td class="value"><?php echo e($delivery->order->order_number); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if($delivery->carrier && $delivery->carrier !== 'TBD'): ?>
                    <tr>
                        <td class="label">Carrier</td>
                        <td class="value"><?php echo e($delivery->carrier); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="label">Status</td>
                        <td class="value"><?php echo e(ucfirst($delivery->status)); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <?php if($delivery->order && $delivery->order->items && count($delivery->order->items) > 0): ?>
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Item Description</th>
                        <th style="width: 20%;" class="text-center">Specifications</th>
                        <th style="width: 12%;" class="text-center">Quantity</th>
                        <th style="width: 13%;" class="text-right">Rate (<?php echo e($delivery->order->currency ?? 'AED'); ?>)</th>
                        <th style="width: 10%;" class="text-center">Discount</th>
                        <th style="width: 20%;" class="text-right">Amount After Discount (<?php echo e($delivery->order->currency ?? 'AED'); ?>)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $delivery->order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="item-description">
                                <?php echo e($item->description ?? $item->product_name); ?>

                                <?php if($item->product): ?>
                                    <div style="font-size: 9px; margin-top: 3px;">
                                        <a href="<?php echo e(route('product.show', $item->product)); ?>" style="color: #0ea5e9; text-decoration: none;">View product page</a>
                                        <?php if($item->product->pdf_file): ?>
                                            <span style="color: #6b7280; margin: 0 5px;">|</span>
                                            <a href="<?php echo e(asset('storage/' . $item->product->pdf_file)); ?>" target="_blank" style="color: #dc2626; text-decoration: none;">
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
                        <td class="text-right"><?php echo e(number_format($item->unit_price ?? 0, 2)); ?></td>
                        <td class="text-center">
                            <?php if(($item->discount_percentage ?? 0) > 0): ?>
                                <?php echo e(number_format($item->discount_percentage, 2)); ?>%
                            <?php else: ?>
                                <span style="color: var(--text-muted);">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php
                                $unitPrice = $item->unit_price ?? 0;
                                $quantity = $item->quantity;
                                $discount = $item->discount_percentage ?? 0;
                                $subtotal = $unitPrice * $quantity;
                                $discountAmount = $subtotal * ($discount / 100);
                                $totalAfterDiscount = $subtotal - $discountAmount;
                            ?>
                            <?php echo e(number_format($totalAfterDiscount, 2)); ?>

                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- DELIVERY NOTES -->
        <?php if($delivery->notes): ?>
        <div class="description-section">
            <div class="description-title">Delivery Notes</div>
            <div class="description-content"><?php echo e($delivery->notes); ?></div>
        </div>
        <?php endif; ?>

        <!-- SIGNATURE SECTION -->
        <?php if($delivery->signed_at): ?>
        <div class="signature-section">
            <div class="signature-wrapper">
                <div class="signature-block">
                    <div class="signature-title">Customer Signature</div>
                    <div class="signature-box">
                        <?php if($delivery->customer_signature): ?>
                            <img src="<?php echo e(public_path('storage/' . $delivery->customer_signature)); ?>" alt="Customer Signature" class="signature-image">
                        <?php else: ?>
                            <span style="color: var(--success-color); font-weight: bold; font-size: 12px;">✓ SIGNED DIGITALLY</span>
                        <?php endif; ?>
                    </div>
                    <div class="signature-info">
                        <div class="signature-date"><?php echo e(formatDubaiDate($delivery->signed_at, 'd M Y \a\t g:i A')); ?></div>
                        <?php if($delivery->delivery_conditions && is_array($delivery->delivery_conditions)): ?>
                            <div style="font-size: 8px; color: var(--text-muted); margin-top: 3px;">
                                Conditions: <?php echo e(implode(', ', $delivery->delivery_conditions)); ?>

                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="signature-block">
                    <div class="signature-title">Authorized Representative</div>
                    <div class="signature-box">
                        <span style="color: var(--primary-color); font-weight: bold; font-size: 12px;">MaxMed UAE</span>
                    </div>
                    <div class="signature-info">
                        <?php if(isset($authorizedUser) && $authorizedUser): ?>
                            <div class="signature-name"><?php echo e($authorizedUser->name); ?></div>
                        <?php elseif(auth()->check()): ?>
                            <div class="signature-name"><?php echo e(auth()->user()->name); ?></div>
                        <?php else: ?>
                            <div class="signature-name">MaxMed Representative</div>
                        <?php endif; ?>
                        <div class="signature-date"><?php echo e(formatDubaiDate($delivery->delivered_at ?? $delivery->created_at, 'd M Y')); ?></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- FOOTER -->
        <div class="footer">
            <p>This delivery note was generated electronically by MaxMed UAE system.</p>
            <p>Thank you for choosing MaxMed UAE - Your trusted laboratory equipment partner</p>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/admin/deliveries/pdf.blade.php ENDPATH**/ ?>