<?php $__env->startSection('content'); ?>
<style>
    @keyframes pulse-feedback {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.7);
        }
        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(22, 163, 74, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
        }
    }
    .animate-pulse-feedback {
        animation: pulse-feedback 2s infinite;
    }
</style>
<div class="bg-gray-50 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 px-4 sm:px-0">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
                <p class="mt-1 text-sm text-gray-600">
                    <?php if($orders->isEmpty()): ?>
                        You haven't placed any orders yet.
                    <?php else: ?>
                        Showing <?php echo e($orders->firstItem() ?? 0); ?> - <?php echo e($orders->lastItem() ?? 0); ?> of <?php echo e($orders->total()); ?> orders
                    <?php endif; ?>
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?php echo e(route('products.index')); ?>" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>

        <!-- Success Message -->
        <?php if(session('success')): ?>
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 mx-4 sm:mx-0 rounded shadow-sm" role="alert">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700"><?php echo e(session('success')); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if($orders->isEmpty()): ?>
            <!-- Empty State -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="py-16 px-4 sm:px-6 lg:px-8 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">No orders yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Start shopping to see your orders here.</p>
                    <div class="mt-6">
                        <a href="<?php echo e(route('products.index')); ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Browse Products
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Orders List -->
            <div class="space-y-4">
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white shadow overflow-hidden sm:rounded-lg hover:shadow-md transition-shadow duration-300">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">
                                        Order #<?php echo e($order->order_number); ?>

                                    </h3>
                                    <div class="mt-1 flex items-center">
                                        <svg class="flex-shrink-0 mr-1.5 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-500">Placed on <?php echo e(formatDubaiDate($order->created_at, 'M d, Y')); ?> (Dubai Time)</p>
                                    </div>
                                </div>
                                <div class="mt-3 sm:mt-0 flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                                        <?php if($order->status === 'pending'): ?> bg-yellow-100 text-yellow-800
                                        <?php elseif($order->status === 'awaiting_quotations'): ?> bg-orange-100 text-orange-800
                                        <?php elseif($order->status === 'quotations_received'): ?> bg-blue-100 text-blue-800
                                        <?php elseif($order->status === 'approved'): ?> bg-indigo-100 text-indigo-800
                                        <?php elseif($order->status === 'processing'): ?> bg-purple-100 text-purple-800
                                        <?php elseif($order->status === 'shipped'): ?> bg-cyan-100 text-cyan-800
                                        <?php elseif($order->status === 'delivered'): ?> bg-green-100 text-green-800
                                        <?php elseif($order->status === 'completed'): ?> bg-emerald-100 text-emerald-800
                                        <?php elseif($order->status === 'cancelled'): ?> bg-red-100 text-red-800
                                        <?php else: ?> bg-gray-100 text-gray-800
                                        <?php endif; ?>">
                                        <?php
                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'awaiting_quotations' => 'Awaiting Quotations',
                                                'quotations_received' => 'Quotations Received',
                                                'approved' => 'Approved',
                                                'processing' => 'Processing',
                                                'shipped' => 'Shipped',
                                                'delivered' => 'Delivered',
                                                'completed' => 'Completed',
                                                'cancelled' => 'Cancelled'
                                            ];
                                        ?>
                                        <?php echo e($statusLabels[$order->status] ?? ucfirst(str_replace('_', ' ', $order->status))); ?>

                                    </span>
                                    <button onclick="generateTrackingLink('<?php echo e($order->id); ?>', '<?php echo e($order->order_number); ?>')" 
                                            class="inline-flex items-center px-2 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                        </svg>
                                        Share Tracking
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <svg class="h-6 w-6 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    <span class="text-gray-600 text-sm">Total Amount</span>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-gray-900"><?php echo e($order->currency ?? 'AED'); ?><?php echo e(number_format($order->total_amount, 2)); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-4 sm:px-6 flex justify-end space-x-3">
                            <a href="<?php echo e(route('orders.show', $order)); ?>" 
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                View Details
                                <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <button onclick="openFeedbackModal('<?php echo e($order->id); ?>', '<?php echo e($order->order_number); ?>')" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-black bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 animate-pulse-feedback">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                Provide Feedback
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Feedback Modal -->
            <div id="feedbackModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" style="z-index: 50;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Provide Feedback for Order #<span id="orderNumber"></span></h3>
                                <button onclick="closeFeedbackModal()" class="text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form action="<?php echo e(route('feedback.store')); ?>" method="POST" class="px-6 py-4">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="order_id" id="orderId">
                            <div class="space-y-4">
                                <div>
                                    <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                                    <select name="rating" id="rating" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="5">Excellent</option>
                                        <option value="4">Very Good</option>
                                        <option value="3">Good</option>
                                        <option value="2">Fair</option>
                                        <option value="1">Poor</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="feedback" class="block text-sm font-medium text-gray-700">Your Feedback</label>
                                    <textarea name="feedback" id="feedback" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please share your experience with this order..."></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" onclick="closeFeedbackModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function openFeedbackModal(orderId, orderNumber) {
                    document.getElementById('orderId').value = orderId;
                    document.getElementById('orderNumber').textContent = orderNumber;
                    document.getElementById('feedbackModal').classList.remove('hidden');
                }

                function closeFeedbackModal() {
                    document.getElementById('feedbackModal').classList.add('hidden');
                }

                // Tracking Link Modal functionality
                function generateTrackingLink(orderId, orderNumber) {
                    console.log('Generating tracking link for order:', orderId, orderNumber);
                    
                    // Generate the tracking URL
                    const baseUrl = window.location.origin;
                    const trackingUrl = `${baseUrl}/track-order/${orderId}`;
                    
                    const modalHTML = `
                        <div id="trackingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                            <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
                                <div class="mt-3">
                                    <!-- Header -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Order Tracking Link</h3>
                                            <p class="text-sm text-gray-600 mt-1">Order #${orderNumber}</p>
                                        </div>
                                        <button onclick="closeTrackingModal()" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Tracking URL -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Shareable Tracking Link</label>
                                        <div class="flex">
                                            <input type="text" id="trackingUrl" value="${trackingUrl}" readonly
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-sm">
                                            <button onclick="copyTrackingLink()" 
                                                    class="px-3 py-2 bg-indigo-600 text-white border border-indigo-600 rounded-r-md hover:bg-indigo-700 text-sm">
                                                Copy
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Share this link with customers who don't have an account</p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end space-x-3">
                                        <button onclick="closeTrackingModal()" 
                                                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            Close
                                        </button>
                                        <button onclick="shareTrackingLink('${trackingUrl}')" 
                                                class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700">
                                            Share Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    document.body.insertAdjacentHTML('beforeend', modalHTML);
                }

                function closeTrackingModal() {
                    const modal = document.getElementById('trackingModal');
                    if (modal) {
                        modal.remove();
                    }
                }

                function copyTrackingLink() {
                    const urlInput = document.getElementById('trackingUrl');
                    urlInput.select();
                    urlInput.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand('copy');
                    
                    // Show feedback
                    const button = event.target;
                    const originalText = button.textContent;
                    button.textContent = 'Copied!';
                    button.classList.add('bg-green-600');
                    button.classList.remove('bg-indigo-600');
                    
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove('bg-green-600');
                        button.classList.add('bg-indigo-600');
                    }, 2000);
                }

                function shareTrackingLink(url) {
                    if (navigator.share) {
                        navigator.share({
                            title: 'Order Tracking',
                            text: 'Track your order status',
                            url: url
                        });
                    } else {
                        // Fallback: copy to clipboard
                        copyTrackingLink();
                    }
                }

                // Close modal when clicking outside
                document.addEventListener('click', function(e) {
                    if (e.target.id === 'trackingModal') {
                        closeTrackingModal();
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/orders/index.blade.php ENDPATH**/ ?>