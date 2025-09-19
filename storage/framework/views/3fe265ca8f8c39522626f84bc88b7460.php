

<?php $__env->startSection('title', 'Track Order #' . $order->order_number); ?>

<?php $__env->startSection('content'); ?>
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Order Tracking</h1>
            <p class="mt-2 text-lg text-gray-600">Track the status of your order</p>
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
                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->user->name ?? 'Guest Customer'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Email</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo e($order->user->email ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="px-6 py-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Order Items</h3>
                <div class="space-y-3">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo e($item->product ? $item->product->name : $item->description); ?>

                                </p>
                                <?php if($item->product && $item->product->brand): ?>
                                    <p class="text-xs text-gray-500">Brand: <?php echo e($item->product->brand->name); ?></p>
                                <?php endif; ?>
                                <?php if($item->size): ?>
                                    <p class="text-xs text-gray-500">Size: <?php echo e($item->size); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900"><?php echo e(number_format($item->quantity)); ?> × <?php echo e($order->currency); ?> <?php echo e(number_format($item->unit_price, 2)); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($order->currency); ?> <?php echo e(number_format($item->quantity * $item->unit_price, 2)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

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
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 
                                            <?php echo e($step['is_completed'] ? 'bg-indigo-600' : 'bg-gray-200'); ?>" 
                                            aria-hidden="true"></span>
                                    <?php endif; ?>
                                    
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                <?php echo e($step['is_completed'] ? 'bg-indigo-600' : ($step['is_active'] ? 'bg-yellow-500' : 'bg-gray-200')); ?>">
                                                <svg class="w-4 h-4 <?php echo e($step['is_completed'] || $step['is_active'] ? 'text-white' : 'text-gray-400'); ?>" 
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Delivery Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Delivery Status</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $order->delivery->status))); ?></p>
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
                                <p class="text-sm font-medium text-gray-900"><?php echo e($order->delivery->tracking_number); ?></p>
                            </div>
                        <?php endif; ?>
                        <?php if($order->delivery->shipping_method): ?>
                            <div>
                                <p class="text-sm text-gray-500">Shipping Method</p>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($order->delivery->shipping_method); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Contact Information -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mt-8">
            <div class="px-6 py-4">
                <div class="text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Need Help?</h3>
                    <p class="text-sm text-gray-600 mb-4">Contact our customer service team for any questions about your order</p>
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <a href="mailto:sales@maxmedme.com" 
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/orders/track.blade.php ENDPATH**/ ?>