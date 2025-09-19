<?php $__env->startSection('title', 'Orders Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
                <p class="text-gray-600 mt-2">Track and manage customer orders and sales</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?php echo e(route('admin.orders.create')); ?>" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Create New Order
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card-hover rounded-xl bg-white shadow-sm ring-1 ring-gray-900/5">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">All Orders</h3>
        </div>
        
        <div class="overflow-hidden">
            <?php if($orders->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($order->order_number); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($order->getCustomerName()); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo e($order->getCustomerEmail()); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($order->currency ?? 'AED'); ?> <?php echo e(number_format($order->total_amount, 2)); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($order->status == 'completed'): ?>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Completed
                                            </span>
                                        <?php elseif($order->status == 'cancelled'): ?>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Cancelled
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                <?php echo e(ucfirst($order->status)); ?>

                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e(formatDubaiDate($order->created_at, 'M d, Y')); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e(formatDubaiDate($order->created_at, 'H:i')); ?> Dubai</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="text-indigo-600 hover:text-indigo-900" title="View Order Details">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </a>
                                            <button onclick="generateAdminTrackingLink('<?php echo e($order->id); ?>', '<?php echo e($order->order_number); ?>', '<?php echo e($order->getCustomerName()); ?>', '<?php echo e($order->getCustomerEmail()); ?>')" 
                                                    class="text-green-600 hover:text-green-900" title="Generate Customer Tracking Link">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                </svg>
                                            </button>
                                            <form action="<?php echo e(route('admin.orders.destroy', $order)); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this order? This action cannot be undone.')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete Order">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($orders->hasPages()): ?>
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if($orders->onFirstPage()): ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                    Previous
                                </span>
                            <?php else: ?>
                                <a href="<?php echo e($orders->previousPageUrl()); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>

                            <?php if($orders->hasMorePages()): ?>
                                <a href="<?php echo e($orders->nextPageUrl()); ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php else: ?>
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-500 bg-white cursor-default">
                                    Next
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium"><?php echo e($orders->firstItem()); ?></span>
                                    to
                                    <span class="font-medium"><?php echo e($orders->lastItem()); ?></span>
                                    of
                                    <span class="font-medium"><?php echo e($orders->total()); ?></span>
                                    orders
                                </p>
                            </div>
                            <div>
                                <?php echo e($orders->links()); ?>

                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No orders found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first order.</p>
                    <div class="mt-6">
                        <a href="<?php echo e(route('admin.orders.create')); ?>" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Create Order
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Admin Tracking Link Generation functionality
function generateAdminTrackingLink(orderId, orderNumber, customerName, customerEmail) {
    console.log('Generating admin tracking link for order:', orderId, orderNumber);
    
    // Generate the tracking URL
    const baseUrl = window.location.origin;
    const trackingUrl = `${baseUrl}/track-order/${orderId}`;
    
    const modalHTML = `
        <div id="adminTrackingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-6 border w-full max-w-lg shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Customer Tracking Link</h3>
                            <p class="text-sm text-gray-600 mt-1">Order #${orderNumber}</p>
                        </div>
                        <button onclick="closeAdminTrackingModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Customer Info -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-1 gap-2">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Customer:</span>
                                <span class="text-sm text-gray-900">${customerName}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Email:</span>
                                <span class="text-sm text-gray-900">${customerEmail}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tracking URL -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Tracking Link</label>
                        <div class="flex">
                            <input type="text" id="adminTrackingUrl" value="${trackingUrl}" readonly
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm">
                            <button onclick="copyAdminTrackingLink()" 
                                    class="px-3 py-2 bg-indigo-600 text-white border border-indigo-600 rounded-r-md hover:bg-indigo-700 text-sm">
                                Copy
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Share this link with customers to track their order without logging in</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quick Actions</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="emailTrackingLink('${customerEmail}', '${trackingUrl}', '${orderNumber}')" 
                                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Email Link
                            </button>
                            <button onclick="openTrackingPreview('${trackingUrl}')" 
                                    class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Preview
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3">
                        <button onclick="closeAdminTrackingModal()" 
                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Close
                        </button>
                        <button onclick="copyAdminTrackingLink()" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                            Copy Link
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

function closeAdminTrackingModal() {
    const modal = document.getElementById('adminTrackingModal');
    if (modal) {
        modal.remove();
    }
}

function copyAdminTrackingLink() {
    const urlInput = document.getElementById('adminTrackingUrl');
    urlInput.select();
    urlInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show feedback
    const buttons = document.querySelectorAll('button[onclick="copyAdminTrackingLink()"]');
    buttons.forEach(button => {
        const originalText = button.textContent.trim();
        button.textContent = 'Copied!';
        button.classList.add('bg-green-600');
        button.classList.remove('bg-indigo-600');
        
        setTimeout(() => {
            button.textContent = originalText;
            button.classList.remove('bg-green-600');
            button.classList.add('bg-indigo-600');
        }, 2000);
    });
}

function emailTrackingLink(customerEmail, trackingUrl, orderNumber) {
    const subject = `Order Tracking - Order #${orderNumber}`;
    const body = `Hello,

You can track the status of your order #${orderNumber} using the following link:

${trackingUrl}

This link will show you real-time updates on your order status, delivery information, and estimated delivery dates.

If you have any questions, please don't hesitate to contact our customer service team.

Best regards,
MaxMed UAE Team`;
    
    const mailtoUrl = `mailto:${customerEmail}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    window.location.href = mailtoUrl;
}

function openTrackingPreview(trackingUrl) {
    window.open(trackingUrl, '_blank');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'adminTrackingModal') {
        closeAdminTrackingModal();
    }
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>