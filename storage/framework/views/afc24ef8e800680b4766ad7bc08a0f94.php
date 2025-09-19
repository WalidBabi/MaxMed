<?php
    use Illuminate\Support\Facades\Storage;
?>

<?php $__env->startSection('title', 'Purchase Order #' . $purchaseOrder->po_number); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Purchase Order #<?php echo e($purchaseOrder->po_number); ?></h1>
                <p class="text-gray-600 mt-2">Created <?php echo e(formatDubaiDate($purchaseOrder->created_at, 'M d, Y \a\t H:i')); ?></p>
            </div>
            <div class="flex items-center space-x-3">
                <?php if($purchaseOrder->canBeEdited()): ?>
                    <a href="<?php echo e(route('admin.purchase-orders.edit', $purchaseOrder)); ?>" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                        <?php if($purchaseOrder->status !== 'draft'): ?>
                            <span class="ml-1 text-xs text-yellow-200">(Sent)</span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if($purchaseOrder->canBeEdited()): ?>
                    <form action="<?php echo e(route('admin.purchase-orders.destroy', $purchaseOrder)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?<?php echo e($purchaseOrder->status !== 'draft' ? ' Note: This purchase order has already been sent to the supplier.' : ''); ?> This action cannot be undone.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                            <?php if($purchaseOrder->status !== 'draft'): ?>
                                <span class="ml-1 text-xs text-yellow-200">(Sent)</span>
                            <?php endif; ?>
                        </button>
                    </form>
                <?php endif; ?>
                
                <a href="<?php echo e(route('admin.purchase-orders.pdf', $purchaseOrder)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
                
                <a href="<?php echo e(route('admin.purchase-orders.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Purchase Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Status & Actions Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div>
                    <span class="text-sm font-medium text-gray-500">Status:</span>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($purchaseOrder->status_badge_class); ?>">
                        <?php echo e(\App\Models\PurchaseOrder::$statuses[$purchaseOrder->status] ?? ucfirst($purchaseOrder->status)); ?>

                    </span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Payment:</span>
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($purchaseOrder->payment_status_badge_class); ?>">
                        <?php echo e(\App\Models\PurchaseOrder::$paymentStatuses[$purchaseOrder->payment_status] ?? ucfirst($purchaseOrder->payment_status)); ?>

                    </span>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Total:</span>
                    <span class="ml-2 text-lg font-bold text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_total); ?></span>
                </div>
            </div>
            
            <div class="flex items-center space-x-2">
                <?php if($purchaseOrder->status === 'draft'): ?>
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 send-email-btn"
                            data-po-id="<?php echo e($purchaseOrder->id); ?>"
                            data-supplier-name="<?php echo e($purchaseOrder->supplier_name); ?>"
                            data-po-number="<?php echo e($purchaseOrder->po_number); ?>"
                            data-supplier-email="<?php echo e($purchaseOrder->supplier_email ?? ''); ?>">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Send Email to Supplier
                    </button>
                <?php endif; ?>
                
                <?php if($purchaseOrder->payment_status === 'pending' || $purchaseOrder->payment_status === 'partial'): ?>
                    <button onclick="openPaymentModal()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Record Payment
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- PO Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Purchase Order Details</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PO Number</label>
                            <p class="text-lg font-semibold text-gray-900"><?php echo e($purchaseOrder->po_number); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">PO Date</label>
                            <p class="text-lg font-semibold text-gray-900"><?php echo e(formatDubaiDate($purchaseOrder->po_date, 'M d, Y')); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Source</label>
                            <?php if($purchaseOrder->hasCustomerOrder()): ?>
                                <div>
                                    <span class="text-sm text-gray-500">Customer Order</span>
                                    <a href="<?php echo e(route('admin.orders.show', $purchaseOrder->order)); ?>" class="block text-lg font-semibold text-indigo-600 hover:text-indigo-700">
                                        <?php echo e($purchaseOrder->order->order_number); ?>

                                    </a>
                                    <p class="text-sm text-gray-500"><?php echo e($purchaseOrder->order->getCustomerName()); ?></p>
                                </div>
                            <?php elseif($purchaseOrder->isFromSupplierInquiry()): ?>
                                <div>
                                    <span class="text-sm text-gray-500">Supplier Inquiry</span>
                                    <p class="text-lg font-semibold text-gray-900">Inquiry #<?php echo e($purchaseOrder->supplier_quotation_id); ?></p>
                                    <?php if($purchaseOrder->supplierQuotation): ?>
                                        <p class="text-sm text-gray-500"><?php echo e($purchaseOrder->supplierQuotation->product->name); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div>
                                    <span class="text-sm text-gray-500">Internal Purchase</span>
                                    <p class="text-lg font-semibold text-gray-900">Direct Purchase</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Requested Delivery</label>
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo e($purchaseOrder->delivery_date_requested ? $purchaseOrder->delivery_date_requested->format('M d, Y') : 'Not specified'); ?>

                            </p>
                        </div>
                    </div>

                    <?php if($purchaseOrder->description): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-900"><?php echo e($purchaseOrder->description); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($purchaseOrder->terms_conditions): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Terms & Conditions</label>
                            <p class="text-gray-900"><?php echo e($purchaseOrder->terms_conditions); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if($purchaseOrder->notes): ?>
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-500">Internal Notes</label>
                            <p class="text-gray-900"><?php echo e($purchaseOrder->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Financial Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_sub_total); ?></span>
                        </div>
                        <?php if($purchaseOrder->tax_amount > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_tax_amount); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if($purchaseOrder->shipping_cost > 0): ?>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_shipping_cost); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-semibold">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_total); ?></span>
                            </div>
                        </div>
                        <?php if($purchaseOrder->paid_amount > 0): ?>
                            <div class="flex justify-between text-green-600">
                                <span>Paid:</span>
                                <span><?php echo e($purchaseOrder->currency); ?> <?php echo e($purchaseOrder->formatted_paid_amount); ?></span>
                            </div>
                            <div class="flex justify-between text-red-600">
                                <span>Outstanding:</span>
                                <span><?php echo e($purchaseOrder->currency); ?> <?php echo e(number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2)); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <?php
                $attachments = is_array($purchaseOrder->attachments) ? $purchaseOrder->attachments : [];
                $attachmentCount = count($attachments);
            ?>
            <?php if($attachmentCount > 0): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Attached Files</h3>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($attachmentCount); ?> file<?php echo e($attachmentCount > 1 ? 's' : ''); ?> attached to this purchase order</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attachment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <?php
                                                    $filename = $attachment['filename'] ?? 'attachment';
                                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                                ?>
                                                
                                                <?php switch($extension):
                                                    case ('pdf'): ?>
                                                        <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <?php break; ?>
                                                    <?php case ('doc'): ?>
                                                    <?php case ('docx'): ?>
                                                        <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <?php break; ?>
                                                    <?php case ('xls'): ?>
                                                    <?php case ('xlsx'): ?>
                                                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <?php break; ?>
                                                    <?php case ('jpg'): ?>
                                                    <?php case ('jpeg'): ?>
                                                    <?php case ('png'): ?>
                                                    <?php case ('gif'): ?>
                                                        <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <?php break; ?>
                                                    <?php default: ?>
                                                        <svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                        </svg>
                                                <?php endswitch; ?>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900 truncate" title="<?php echo e($attachment['filename'] ?? 'Attachment'); ?>">
                                                <?php echo e($attachment['filename'] ?? 'Attachment'); ?>

                                            </h4>
                                            <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500">
                                                <?php if(isset($attachment['size'])): ?>
                                                    <span><?php echo e(number_format($attachment['size'] / 1024, 1)); ?> KB</span>
                                                <?php endif; ?>
                                                <?php if(isset($attachment['type'])): ?>
                                                    <span>•</span>
                                                    <span><?php echo e($attachment['type']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if(isset($attachment['path'])): ?>
                                                <div class="mt-2">
                                                    <a href="<?php echo e(Storage::url($attachment['path'])); ?>" 
                                                       target="_blank" 
                                                       class="inline-flex items-center text-xs text-indigo-600 hover:text-indigo-700">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                        </svg>
                                                        View File
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payments History -->
            <?php if($purchaseOrder->payments->count() > 0): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $purchaseOrder->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="<?php echo e(route('admin.supplier-payments.show', $payment)); ?>" class="text-indigo-600 hover:text-indigo-700">
                                                <?php echo e($payment->payment_number); ?>

                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                            <?php echo e($payment->currency); ?> <?php echo e($payment->formatted_amount); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                            <?php echo e(ucfirst($payment->payment_method)); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($payment->status_badge_class); ?>">
                                                <?php echo e(\App\Models\SupplierPayment::$statuses[$payment->status] ?? ucfirst($payment->status)); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                            <?php echo e(formatDubaiDate($payment->payment_date, 'M d, Y')); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Supplier Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Information</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="text-gray-900"><?php echo e($purchaseOrder->supplier_name); ?></p>
                    </div>
                    <?php if($purchaseOrder->supplier_email): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <a href="mailto:<?php echo e($purchaseOrder->supplier_email); ?>" class="text-indigo-600 hover:text-indigo-700">
                                <?php echo e($purchaseOrder->supplier_email); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if($purchaseOrder->supplier_phone): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <a href="tel:<?php echo e($purchaseOrder->supplier_phone); ?>" class="text-indigo-600 hover:text-indigo-700">
                                <?php echo e($purchaseOrder->supplier_phone); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if($purchaseOrder->supplier_address): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="text-gray-900"><?php echo e($purchaseOrder->supplier_address); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-500">Created</p>
                                            <p class="text-xs text-gray-400"><?php echo e(formatDubaiDate($purchaseOrder->created_at, 'M d, Y H:i')); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </li>

                            <?php if($purchaseOrder->sent_to_supplier_at): ?>
                                <li>
                                    <div class="relative pb-8">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-gray-500">Sent to Supplier</p>
                                                <p class="text-xs text-gray-400"><?php echo e(formatDubaiDate($purchaseOrder->sent_to_supplier_at, 'M d, Y H:i')); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>

                            <?php if($purchaseOrder->acknowledged_at): ?>
                                <li>
                                    <div class="relative">
                                        <div class="relative flex space-x-3">
                                            <div class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm text-gray-500">Acknowledged by Supplier</p>
                                                <p class="text-xs text-gray-400"><?php echo e(formatDubaiDate($purchaseOrder->acknowledged_at, 'M d, Y H:i')); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Payment History -->
            <?php if($purchaseOrder->payments && $purchaseOrder->payments->count() > 0): ?>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                                <p class="text-sm text-gray-600 mt-1"><?php echo e($purchaseOrder->payments->count()); ?> payment<?php echo e($purchaseOrder->payments->count() > 1 ? 's' : ''); ?> recorded</p>
                            </div>
                            <?php if($purchaseOrder->payment_status === 'pending' || $purchaseOrder->payment_status === 'partial'): ?>
                                <button onclick="openPaymentModal()" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Record Payment
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attachments</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $purchaseOrder->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($payment->payment_number); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></div>
                                            <?php if($payment->bank_name): ?>
                                                <div class="text-sm text-gray-500"><?php echo e($payment->bank_name); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($payment->status_badge_class); ?>">
                                                <?php echo e(\App\Models\SupplierPayment::$statuses[$payment->status] ?? ucfirst($payment->status)); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e(formatDubaiDate($payment->payment_date, 'M d, Y')); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e($payment->reference_number ?: 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php if($payment->attachments && count($payment->attachments) > 0): ?>
                                                <button onclick="openAttachmentModal('<?php echo e($payment->payment_number); ?>', <?php echo e(json_encode($payment->attachments)); ?>)"
                                                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                    </svg>
                                                    <?php echo e(count($payment->attachments)); ?> file<?php echo e(count($payment->attachments) > 1 ? 's' : ''); ?>

                                                </button>
                                            <?php else: ?>
                                                <span class="text-gray-400">No attachments</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit History Section -->
    <?php if($purchaseOrder->edits->count() > 0): ?>
    <div class="mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Edit History
                </h3>
                <p class="text-sm text-gray-600 mt-1"><?php echo e($purchaseOrder->edits->count()); ?> edit<?php echo e($purchaseOrder->edits->count() > 1 ? 's' : ''); ?> made to this purchase order</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php $__currentLoopData = $purchaseOrder->edits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $edit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900"><?php echo e($edit->editor->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($edit->created_at->format('M d, Y \a\t H:i')); ?></p>
                            </div>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($edit->change_description); ?></p>
                            <?php if($edit->edit_reason): ?>
                                <div class="mt-2 p-2 bg-yellow-50 rounded border border-yellow-200">
                                    <p class="text-xs text-yellow-800"><strong>Reason:</strong> <?php echo e($edit->edit_reason); ?></p>
                                </div>
                            <?php endif; ?>
                            <div class="mt-2 text-xs text-gray-500">
                                Status when edited: <span class="font-medium"><?php echo e(\App\Models\PurchaseOrder::$statuses[$edit->po_status_when_edited] ?? ucfirst($edit->po_status_when_edited)); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Record Payment to Supplier</h3>
                    <p class="text-sm text-gray-600 mt-1">Record a payment made to <?php echo e($purchaseOrder->supplier_name); ?></p>
                </div>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-sm text-gray-500">Total Amount</div>
                        <div class="text-lg font-semibold text-gray-900"><?php echo e($purchaseOrder->currency); ?> <?php echo e(number_format($purchaseOrder->total_amount, 2)); ?></div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Paid Amount</div>
                        <div class="text-lg font-semibold text-green-600"><?php echo e($purchaseOrder->currency); ?> <?php echo e(number_format($purchaseOrder->paid_amount, 2)); ?></div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">Remaining</div>
                        <div class="text-lg font-semibold text-red-600"><?php echo e($purchaseOrder->currency); ?> <?php echo e(number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2)); ?></div>
                    </div>
                </div>
            </div>

            <form action="<?php echo e(route('admin.purchase-orders.create-payment', $purchaseOrder)); ?>" method="POST" id="paymentForm" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                            <label for="payment_amount" class="block text-sm font-medium text-gray-700">Payment Amount *</label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm"><?php echo e($purchaseOrder->currency); ?></span>
                                </div>
                                <input type="number" id="payment_amount" name="amount" step="0.01" min="0.01" 
                               max="<?php echo e($purchaseOrder->total_amount - $purchaseOrder->paid_amount); ?>" 
                               value="<?php echo e($purchaseOrder->total_amount - $purchaseOrder->paid_amount); ?>" required
                                       class="pl-12 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum: <?php echo e($purchaseOrder->currency); ?> <?php echo e(number_format($purchaseOrder->total_amount - $purchaseOrder->paid_amount, 2)); ?></p>
                        </div>

                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required
                                    class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="bank_transfer">Bank Transfer</option>
                                <option value="online_transfer">Online Transfer</option>
                            <option value="check">Check</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="cash">Cash</option>
                                <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date *</label>
                        <input type="date" id="payment_date" name="payment_date" value="<?php echo e(date('Y-m-d')); ?>" required
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                            <input type="text" id="reference_number" name="reference_number" placeholder="Transaction ID, Check number, etc."
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                            <input type="text" id="bank_name" name="bank_name" placeholder="Bank or financial institution"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                            <input type="text" id="account_number" name="account_number" placeholder="Account or card number"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="transaction_id" class="block text-sm font-medium text-gray-700">Transaction ID</label>
                            <input type="text" id="transaction_id" name="transaction_id" placeholder="Unique transaction identifier"
                                   class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea id="notes" name="notes" rows="3" placeholder="Additional payment details or comments"
                                      class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div>
                            <label for="payment_attachments" class="block text-sm font-medium text-gray-700">Attachments</label>
                            <input type="file" id="payment_attachments" name="attachments[]" multiple 
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.xls,.xlsx"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Max 10MB per file. Supported: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closePaymentModal()" 
                            class="px-6 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Send Email Modal -->
<div x-data="{ show: false, isLoading: false }" 
     x-on:open-modal.window="console.log('Modal event received:', $event.detail); $event.detail == 'send-po-email' ? show = true : null" 
     x-on:close-modal.window="$event.detail == 'send-po-email' ? show = false : null" 
     x-show="show" 
     class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" 
     style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:max-w-2xl sm:mx-auto border border-gray-200" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        <form id="sendEmailForm" method="POST" action="" x-data="{ submitting: false }" @submit.prevent="submitEmailForm($event)">
            <?php echo csrf_field(); ?>
            
            <!-- Header with gradient background -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white bg-opacity-20 backdrop-blur-sm">
                        <svg class="h-7 w-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1 text-white">
                        <h3 class="text-xl font-semibold">Send Purchase Order Email</h3>
                        <p class="text-sm mt-1">Send the purchase order directly to your supplier's email</p>
                    </div>
                    <button type="button" x-on:click="show = false" class="rounded-full p-2 hover:bg-white hover:bg-opacity-20 transition-colors duration-200 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
                <div class="space-y-6">
                    <!-- Purchase Order Information Card -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900" id="emailModalPoNumber">Purchase Order #</h4>
                                <p class="text-sm text-gray-600" id="emailModalSupplierName">Supplier Name</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Fields -->
                    <div class="space-y-5">
                        <div>
                            <label for="supplier_email" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                                Supplier Email Address
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input type="email" 
                                       id="supplier_email" 
                                       name="supplier_email" 
                                       required
                                       class="block w-full px-4 py-3 pr-10 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" 
                                       placeholder="Enter supplier email address">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <div id="emailLoadingSpinner" class="hidden">
                                        <svg class="animate-spin h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <svg id="emailFoundIcon" class="hidden h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <p id="emailStatus" class="mt-2 text-sm text-gray-600 hidden"></p>
                        </div>
                        
                        <div>
                            <label for="cc_emails" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                CC Email Addresses
                            </label>
                            <input type="text" 
                                   id="cc_emails" 
                                   name="cc_emails" 
                                   value="sales@maxmedme.com" 
                                   class="block w-full px-4 py-3 text-gray-900 placeholder-gray-500 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200"
                                   placeholder="Additional email addresses (comma separated)">
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                Separate multiple email addresses with commas
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                <div class="flex items-center justify-between">
                    <button type="button" 
                            x-on:click="show = false" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                    <button type="submit" 
                            x-bind:disabled="submitting"
                            class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 border border-transparent rounded-lg shadow-lg hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                        <span x-show="!submitting" class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Email
                        </span>
                        <span x-show="submitting" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Email functionality
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced send email functionality
    document.querySelectorAll('.send-email-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Send email button clicked for purchase orders');
            const poId = this.getAttribute('data-po-id');
            const supplierName = this.getAttribute('data-supplier-name');
            const poNumber = this.getAttribute('data-po-number');
            const supplierEmail = this.getAttribute('data-supplier-email');
            
            console.log('PO data:', { poId, supplierName, poNumber, supplierEmail });
            
            const sendEmailForm = document.getElementById('sendEmailForm');
            if (!sendEmailForm) {
                console.error('Send email form not found');
                return;
            }
            
            sendEmailForm.action = `/admin/purchase-orders/${poId}/send-email`;
            console.log('Form action set to:', sendEmailForm.action);
            
            // Update modal content
            const emailModalPoNumber = document.getElementById('emailModalPoNumber');
            const emailModalSupplierName = document.getElementById('emailModalSupplierName');
            
            if (emailModalPoNumber) emailModalPoNumber.textContent = `Purchase Order ${poNumber}`;
            if (emailModalSupplierName) emailModalSupplierName.textContent = supplierName;
            
            // Use existing email or populate field
            if (supplierEmail && supplierEmail.trim() !== '') {
                populateEmailField(supplierEmail, 'Supplier email loaded from purchase order');
            } else {
                // Clear email field if no email found
                const emailInput = document.getElementById('supplier_email');
                if (emailInput) {
                    emailInput.value = '';
                }
            }
            
            console.log('Dispatching open-modal event for send-po-email');
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'send-po-email' }));
        });
    });

    // Helper function to populate email field
    function populateEmailField(email, message) {
        const emailInput = document.getElementById('supplier_email');
        const loadingSpinner = document.getElementById('emailLoadingSpinner');
        const emailFoundIcon = document.getElementById('emailFoundIcon');
        const emailStatus = document.getElementById('emailStatus');
        
        if (loadingSpinner) loadingSpinner.classList.add('hidden');
        if (emailInput) {
            emailInput.disabled = false;
            emailInput.value = email;
        }
        if (emailFoundIcon) emailFoundIcon.classList.remove('hidden');
        if (emailStatus) {
            emailStatus.className = 'mt-2 text-sm text-green-600 flex items-center';
            emailStatus.innerHTML = `
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                ${message}
            `;
            emailStatus.classList.remove('hidden');
        }
    }
});

// Form submission handler
function submitEmailForm(event) {
    const form = event.target;
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            alert(data.message || 'Purchase Order email sent successfully!');
            
            // Close modal
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'send-po-email' }));
            
            // Optionally reload page to update status
            if (data.reload) {
                location.reload();
            }
        } else {
            throw new Error(data.message || 'Failed to send email');
        }
    })
    .catch(error => {
        console.error('Error sending email:', error);
        alert('Failed to send email: ' + error.message);
    });
}

// Payment modal functionality
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Auto-open payment modal if URL has #payment hash
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash === '#payment') {
        // Small delay to ensure page is fully loaded
        setTimeout(function() {
            openPaymentModal();
            // Remove the hash from URL without triggering page reload
            history.replaceState(null, null, window.location.pathname);
        }, 100);
    }
});

// Attachment modal functionality
function openAttachmentModal(paymentNumber, attachments) {
    console.log('Opening attachment modal for payment:', paymentNumber, attachments);
    
    // Create modal HTML
    const modalHTML = `
        <div id="attachmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-4xl shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Payment Attachments</h3>
                            <p class="text-sm text-gray-600 mt-1">Payment #${paymentNumber} - ${attachments.length} file${attachments.length > 1 ? 's' : ''}</p>
                        </div>
                        <button onclick="closeAttachmentModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Attachments Grid -->
                    <div class="max-h-96 overflow-y-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            ${attachments.map(attachment => {
                                const extension = attachment.original_name.split('.').pop().toLowerCase();
                                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                                const isPdf = extension === 'pdf';
                                const fileUrl = '/storage/' + attachment.path;
                                
                                let iconColor = 'text-gray-600';
                                let bgColor = 'bg-gray-100';
                                let icon = 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z';
                                
                                if (isPdf) {
                                    iconColor = 'text-red-600';
                                    bgColor = 'bg-red-100';
                                    icon = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                                } else if (isImage) {
                                    iconColor = 'text-blue-600';
                                    bgColor = 'bg-blue-100';
                                    icon = 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z';
                                } else if (['doc', 'docx'].includes(extension)) {
                                    iconColor = 'text-blue-800';
                                    bgColor = 'bg-blue-100';
                                } else if (['xls', 'xlsx'].includes(extension)) {
                                    iconColor = 'text-green-600';
                                    bgColor = 'bg-green-100';
                                }
                                
                                return `
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex flex-col items-center">
                                            ${isImage ? 
                                                `<div class="w-full h-32 mb-3 rounded-lg overflow-hidden bg-gray-100">
                                                    <img src="${fileUrl}" alt="${attachment.original_name}" 
                                                         class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform"
                                                         onclick="openImagePreview('${fileUrl}', '${attachment.original_name}')">
                                                </div>` :
                                                `<div class="w-16 h-16 ${bgColor} rounded-lg flex items-center justify-center mb-3">
                                                    <svg class="w-8 h-8 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"></path>
                                                    </svg>
                                                </div>`
                                            }
                                            <div class="text-center">
                                                <p class="text-sm font-medium text-gray-900 mb-2 truncate w-full" title="${attachment.original_name}">
                                                    ${attachment.original_name.length > 20 ? attachment.original_name.substring(0, 20) + '...' : attachment.original_name}
                                                </p>
                                                <div class="flex space-x-2">
                                                    ${isPdf ? 
                                                        `<button onclick="openPdfViewer('${fileUrl}', '${attachment.original_name}')" 
                                                                class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                                                            View PDF
                                                        </button>` :
                                                        `<a href="${fileUrl}" target="_blank" 
                                                           class="px-3 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors">
                                                            Open
                                                        </a>`
                                                    }
                                                    <a href="${fileUrl}" download="${attachment.original_name}"
                                                       class="px-3 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 transition-colors">
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                        <button onclick="closeAttachmentModal()" 
                                class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeAttachmentModal() {
    const modal = document.getElementById('attachmentModal');
    if (modal) {
        modal.remove();
    }
}

function openImagePreview(imageUrl, imageName) {
    const previewHTML = `
        <div id="imagePreview" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-60" onclick="closeImagePreview()">
            <div class="relative max-w-4xl max-h-full p-4" onclick="event.stopPropagation()">
                <button onclick="closeImagePreview()" class="absolute top-2 right-2 text-white hover:text-gray-300 z-10">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img src="${imageUrl}" alt="${imageName}" class="max-w-full max-h-full object-contain rounded-lg">
                <div class="text-center mt-2">
                    <p class="text-white text-sm">${imageName}</p>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', previewHTML);
}

function closeImagePreview() {
    const preview = document.getElementById('imagePreview');
    if (preview) {
        preview.remove();
    }
}

function openPdfViewer(pdfUrl, pdfName) {
    const pdfViewerHTML = `
        <div id="pdfViewer" class="fixed inset-0 bg-white z-60">
            <div class="flex flex-col h-full">
                <div class="bg-gray-800 text-white p-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium">${pdfName}</h3>
                    <div class="flex space-x-2">
                        <a href="${pdfUrl}" target="_blank" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Open in New Tab
                        </a>
                        <button onclick="closePdfViewer()" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
                <iframe src="${pdfUrl}" class="flex-1 w-full border-none" title="${pdfName}"></iframe>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', pdfViewerHTML);
}

function closePdfViewer() {
    const viewer = document.getElementById('pdfViewer');
    if (viewer) {
        viewer.remove();
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'attachmentModal') {
        closeAttachmentModal();
    }
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/admin/purchase-orders/show.blade.php ENDPATH**/ ?>