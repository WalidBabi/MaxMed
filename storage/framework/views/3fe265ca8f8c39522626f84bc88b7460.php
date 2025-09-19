

<?php $__env->startSection('title', 'Track Order #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Order Tracking</h1>
            <p class="mt-2 text-lg text-gray-600">Complete order information and status</p>
        </div>

        <!-- Order Information Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                    <div>
                        <h2 class="text-xl font-bold">Order #<?php echo e($order->order_number); ?></h2>
                        <p class="text-indigo-100">Placed on <?php echo e(formatDubaiDate($order->created_at, 'M d, Y \a\t H:i')); ?> (Dubai Time)</p>
                    </div>
                    <div class="mt-3 sm:mt-0">
                        <div class="text-right">
                            <p class="text-indigo-100 text-sm">Total Amount</p>
                            <p class="text-2xl font-bold"><?php echo e($order->currency ?? 'AED'); ?> <?php echo e(number_format($order->total_amount, 2)); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Customer Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Customer Name</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->getCustomerName()); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->getCustomerEmail()); ?></p>
                    </div>
                    <?php if($order->shipping_address): ?>
                        <div class="sm:col-span-2">
                            <p class="text-sm text-gray-500">Shipping Address</p>
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo e($order->shipping_address); ?><br>
                                <?php echo e($order->shipping_city); ?>, <?php echo e($order->shipping_state); ?> <?php echo e($order->shipping_zipcode); ?>

                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Order Items and Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Order Items with Product Details -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($order->items->count()); ?> item<?php echo e($order->items->count() > 1 ? 's' : ''); ?> in this order</p>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-6">
                                <div class="flex space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        <?php if($item->product && $item->product->image_url): ?>
                                            <img src="<?php echo e($item->product->image_url); ?>" 
                                                 alt="<?php echo e($item->product->name); ?>"
                                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        <?php elseif($item->product && $item->product->images && $item->product->images->count() > 0): ?>
                                            <img src="<?php echo e($item->product->images->first()->image_url); ?>" 
                                                 alt="<?php echo e($item->product->name); ?>"
                                                 class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                                        <?php else: ?>
                                            <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-base font-medium text-gray-900">
                                                    <?php echo e($item->product ? $item->product->name : $item->description); ?>

                                                </h4>
                                                
                                                <?php if($item->product): ?>
                                                    <!-- Product Details -->
                                                    <?php if($item->product->brand): ?>
                                                        <p class="text-sm text-gray-600 mt-1">
                                                            <span class="font-medium">Brand:</span> <?php echo e($item->product->brand->name); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($item->product->category): ?>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Category:</span> <?php echo e($item->product->category->name); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <?php if($item->product->sku): ?>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">SKU:</span> <?php echo e($item->product->sku); ?>

                                                        </p>
                                                    <?php endif; ?>
                                                    
                                                    <!-- Product Link -->
                                                    <div class="mt-2">
                                                        <a href="<?php echo e(route('product.show', $item->product)); ?>" 
                                                           target="_blank"
                                                           class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                            View Product Details
                                                        </a>
                                                        
                                                        <?php if($item->product->pdf_file): ?>
                                                            <span class="text-gray-400 mx-2">|</span>
                                                            <a href="<?php echo e(asset('storage/' . $item->product->pdf_file)); ?>" 
                                                               target="_blank"
                                                               class="inline-flex items-center text-sm text-red-600 hover:text-red-800">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                                </svg>
                                                                Product Specifications PDF
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Item Specifications -->
                                                <?php if($item->size || $item->specifications): ?>
                                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                                        <h5 class="text-sm font-medium text-gray-900 mb-2">Item Specifications</h5>
                                                        <?php if($item->size): ?>
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-medium">Size:</span> <?php echo e($item->size); ?>

                                                            </p>
                                                        <?php endif; ?>
                                                        <?php if($item->specifications): ?>
                                                            <p class="text-sm text-gray-600">
                                                                <span class="font-medium">Specifications:</span> <?php echo e($item->specifications); ?>

                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <!-- Product Specifications -->
                                                <?php if($item->product && $item->product->specifications && $item->product->specifications->count() > 0): ?>
                                                    <div class="mt-3">
                                                        <button onclick="toggleSpecs('item-<?php echo e($item->id); ?>')" 
                                                                class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            View Product Specifications
                                                        </button>
                                                        <div id="specs-item-<?php echo e($item->id); ?>" class="hidden mt-2 p-3 bg-blue-50 rounded-lg">
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                <?php $__currentLoopData = $item->product->specifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <div class="text-sm">
                                                                        <span class="font-medium text-gray-700"><?php echo e($spec->name); ?>:</span>
                                                                        <span class="text-gray-600"><?php echo e($spec->value); ?></span>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Pricing Information -->
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900"><?php echo e(number_format($item->quantity)); ?> × <?php echo e($order->currency); ?> <?php echo e(number_format($item->unit_price, 2)); ?></p>
                                                <p class="text-lg font-bold text-gray-900"><?php echo e($order->currency); ?> <?php echo e(number_format($item->quantity * $item->unit_price, 2)); ?></p>
                                                <?php if($item->discount_percentage && $item->discount_percentage > 0): ?>
                                                    <p class="text-xs text-green-600"><?php echo e($item->discount_percentage); ?>% discount applied</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- Related Invoices -->
                <?php if($order->invoices && $order->invoices->count() > 0): ?>
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Related Invoices</h3>
                            <p class="text-sm text-gray-600 mt-1"><?php echo e($order->invoices->count()); ?> invoice<?php echo e($order->invoices->count() > 1 ? 's' : ''); ?> for this order</p>
                        </div>
                        
                        <div class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $order->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-medium text-gray-900"><?php echo e($invoice->invoice_number); ?></h4>
                                                    <p class="text-sm text-gray-600"><?php echo e(ucfirst($invoice->type)); ?> Invoice</p>
                                                    <div class="flex items-center space-x-2 mt-1">
                                                        <?php
                                                            $invoiceStatusClass = $invoice->status === 'sent' ? 'bg-green-100 text-green-800' : ($invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800');
                                                            $paymentStatusClass = $invoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                                        ?>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($invoiceStatusClass); ?>">
                                                            <?php echo e(ucfirst($invoice->status)); ?>

                                                        </span>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?php echo e($paymentStatusClass); ?>">
                                                            Payment: <?php echo e(ucfirst($invoice->payment_status)); ?>

                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center space-x-2">
                                            <div class="text-right mr-4">
                                                <p class="text-sm font-medium text-gray-900"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->total_amount, 2)); ?></p>
                                                <?php if($invoice->payment_status === 'paid'): ?>
                                                    <p class="text-xs text-green-600">Fully Paid</p>
                                                <?php elseif($invoice->payment_status === 'partial'): ?>
                                                    <p class="text-xs text-yellow-600"><?php echo e($invoice->currency); ?> <?php echo e(number_format($invoice->paid_amount, 2)); ?> paid</p>
                                                <?php else: ?>
                                                    <p class="text-xs text-red-600">Payment Pending</p>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Invoice Actions -->
                                            <a href="<?php echo e(route('admin.invoices.pdf', $invoice)); ?>" 
                                               target="_blank"
                                               class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download PDF
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment History -->
                                    <?php if($invoice->payments && $invoice->payments->count() > 0): ?>
                                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                            <h5 class="text-sm font-medium text-gray-900 mb-2">Payment History</h5>
                                            <div class="space-y-2">
                                                <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="flex items-center justify-between text-sm">
                                                        <div>
                                                            <span class="font-medium"><?php echo e($payment->payment_number); ?></span>
                                                            <span class="text-gray-500">- <?php echo e(formatDubaiDate($payment->payment_date, 'M d, Y')); ?></span>
                                                        </div>
                                                        <div class="text-right">
                                                            <span class="font-medium"><?php echo e($payment->currency); ?> <?php echo e(number_format($payment->amount, 2)); ?></span>
                                                            <span class="text-xs text-gray-500 block"><?php echo e(ucfirst(str_replace('_', ' ', $payment->payment_method))); ?></span>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Order Summary -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Order Summary</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium"><?php echo e($order->currency); ?> <?php echo e(number_format($order->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2)); ?></span>
                            </div>
                            <?php if($order->shipping_rate > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Shipping</span>
                                    <span class="font-medium"><?php echo e($order->currency); ?> <?php echo e(number_format($order->shipping_rate, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if($order->vat_amount > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">VAT</span>
                                    <span class="font-medium"><?php echo e($order->currency); ?> <?php echo e(number_format($order->vat_amount, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if($order->customs_clearance_fee > 0): ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Customs Clearance</span>
                                    <span class="font-medium"><?php echo e($order->currency); ?> <?php echo e(number_format($order->customs_clearance_fee, 2)); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-base font-medium text-gray-900">Total</span>
                                    <span class="text-lg font-bold text-gray-900"><?php echo e($order->currency); ?> <?php echo e(number_format($order->total_amount, 2)); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status Timeline and Delivery -->
            <div class="space-y-8">
                <!-- Order Status Timeline -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Order Status Timeline</h3>
                        <p class="text-sm text-gray-600 mt-1">Track your order progress</p>
                    </div>
                    
                    <div class="px-6 py-6">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                <?php $__currentLoopData = $statusProgression; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <div class="relative pb-8">
                                            <?php if(!$loop->last): ?>
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 <?php echo e($step['is_completed'] ? 'bg-indigo-600' : 'bg-gray-200'); ?>" aria-hidden="true"></span>
                                            <?php endif; ?>
                                            
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <?php
                                                        $circleClass = $step['is_completed'] ? 'bg-indigo-600' : ($step['is_active'] ? 'bg-yellow-500' : 'bg-gray-200');
                                                        $iconClass = $step['is_completed'] || $step['is_active'] ? 'text-white' : 'text-gray-400';
                                                    ?>
                                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white <?php echo e($circleClass); ?>">
                                                        <svg class="w-4 h-4 <?php echo e($iconClass); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo e($step['icon']); ?>"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5">
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900"><?php echo e($step['label']); ?></p>
                                                        <p class="text-sm text-gray-500"><?php echo e($step['description']); ?></p>
                                                        <?php if($step['is_active']): ?>
                                                            <div class="mt-2">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    Current Status
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Delivery Information -->
                <?php if($order->delivery): ?>
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Delivery Information</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Delivery Status</p>
                                    <?php
                                        $deliveryStatusClass = $order->delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->delivery->status === 'shipped' ? 'bg-blue-100 text-blue-800' : ($order->delivery->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($deliveryStatusClass); ?>">
                                        <?php echo e(ucfirst(str_replace('_', ' ', $order->delivery->status))); ?>

                                    </span>
                                </div>
                                <?php if($order->delivery->estimated_delivery_date): ?>
                                    <div>
                                        <p class="text-sm text-gray-500">Estimated Delivery</p>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e(formatDubaiDate($order->delivery->estimated_delivery_date, 'M d, Y')); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if($order->delivery->tracking_number): ?>
                                    <div>
                                        <p class="text-sm text-gray-500">Tracking Number</p>
                                        <p class="text-sm font-medium text-gray-900 font-mono"><?php echo e($order->delivery->tracking_number); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if($order->delivery->shipping_method): ?>
                                    <div>
                                        <p class="text-sm text-gray-500">Shipping Method</p>
                                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->delivery->shipping_method); ?></p>
                                    </div>
                                <?php endif; ?>
                                <?php if($order->delivery->notes): ?>
                                    <div>
                                        <p class="text-sm text-gray-500">Delivery Notes</p>
                                        <p class="text-sm text-gray-700"><?php echo e($order->delivery->notes); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Contact Information -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4">
                        <div class="text-center">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Need Help?</h3>
                            <p class="text-sm text-gray-600 mb-4">Contact our customer service team for any questions about your order</p>
                            <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                                <a href="mailto:sales@maxmedme.com?subject=Order Inquiry - <?php echo e($order->order_number); ?>" 
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Email Support
                                </a>
                                <a href="tel:+971-4-123-4567" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    Call Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Website -->
        <div class="text-center mt-8">
            <a href="<?php echo e(route('products.index')); ?>" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
function toggleSpecs(itemId) {
    const specsDiv = document.getElementById('specs-' + itemId);
    if (specsDiv) {
        specsDiv.classList.toggle('hidden');
    }
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/orders/track.blade.php ENDPATH**/ ?>