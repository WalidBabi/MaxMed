<!-- Pipeline View -->
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $statusCounts['all'] }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Awaiting Quotations Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Needs Quotation</p>
                    <p class="mt-1 text-2xl font-semibold text-orange-600">{{ $statusCounts['awaiting_quotations'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quotations Received Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Approval</p>
                    <p class="mt-1 text-2xl font-semibold text-yellow-600">{{ $statusCounts['quotations_received'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Processing Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Processing</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $statusCounts['processing'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Shipped Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Shipped</p>
                    <p class="mt-1 text-2xl font-semibold text-indigo-600">{{ $statusCounts['shipped'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Completed Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completed</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $statusCounts['completed'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Container with Scroll Buttons -->
    <div class="relative">
        <!-- Left Scroll Button -->
        <button id="scrollLeftBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-opacity duration-200 opacity-0">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <!-- Right Scroll Button -->
        <button id="scrollRightBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-opacity duration-200 opacity-0">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Pipeline Scroll Container -->
        <div class="pipeline-scroll-container overflow-x-auto flex space-x-6 pb-6" id="pipelineContainer">
            @foreach([
                'awaiting_quotations' => ['color' => 'orange', 'title' => 'Needs Quotation'],
                'quotations_received' => ['color' => 'yellow', 'title' => 'Pending Approval'], 
                'processing' => ['color' => 'blue', 'title' => 'Processing'],
                'shipped' => ['color' => 'indigo', 'title' => 'Shipped'],
                'delivered' => ['color' => 'purple', 'title' => 'Delivered'],
                'completed' => ['color' => 'green', 'title' => 'Completed']
            ] as $status => $config)
                @php $color = $config['color']; $title = $config['title']; @endphp
                <div class="flex-shrink-0 w-80">
                    <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-t-lg p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-{{ $color }}-800">
                                {{ $title }}
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $statusCounts[$status] ?? 0 }}
                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="bg-{{ $color }}-25 border-l border-r border-b border-{{ $color }}-200 rounded-b-lg min-h-96 p-4 space-y-4 drop-zone"
                         data-status="{{ $status }}"
                         role="list"
                         aria-label="{{ $title }} orders"
                         tabindex="-1">
                        @if(isset($ordersGrouped[$status]) && $ordersGrouped[$status]->isNotEmpty())
                            @foreach($ordersGrouped[$status] as $order)
                                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md focus-within:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-{{ $color }}-500 transition-all duration-200 order-card"
                                     data-order-id="{{ $order->id }}"
                                     data-current-status="{{ $status }}"
                                     role="listitem"
                                     tabindex="0"
                                     aria-label="Order {{ $order->order_number }}">
                                    
                                    <!-- Order Header -->
                                    <div class="px-4 py-3 bg-{{ $color }}-50 border-b border-{{ $color }}-100">
                                        <h3 class="text-base font-semibold text-{{ $color }}-900 line-clamp-1">
                                            Order {{ $order->order_number }}
                                        </h3>
                                        <p class="text-sm text-{{ $color }}-700">
                                            {{ number_format($order->total_amount, 2) }} {{ $order->currency ?? 'AED' }}
                                        </p>
                                    </div>

                                    <!-- Order Details -->
                                    <div class="p-4">
                                        <div class="space-y-3">
                                            @if($order->items && $order->items->count() > 0)
                                                <div class="space-y-2">
                                                    <h4 class="text-sm font-medium text-gray-900">Items ({{ $order->items->count() }})</h4>
                                                    @foreach($order->items->take(3) as $item)
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <div class="flex-shrink-0 w-8 h-8 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                                </svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="font-medium truncate">{{ $item->product->name ?? 'Product' }}</p>
                                                                <p class="text-xs text-gray-500">Qty: {{ $item->quantity }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if($order->items->count() > 3)
                                                        <p class="text-xs text-gray-500">+{{ $order->items->count() - 3 }} more items</p>
                                                    @endif
                                                </div>
                                            @endif

                                            <!-- Purchase Order Info if exists -->
                                            @if($order->purchaseOrder)
                                                <div class="bg-gray-50 rounded-lg p-3">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">Purchase Order</p>
                                                            <p class="text-xs text-gray-600">{{ $order->purchaseOrder->po_number }}</p>
                                                        </div>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ ucfirst(str_replace('_', ' ', $order->purchaseOrder->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Timeline Info -->
                                            <div class="flex items-center text-sm text-gray-500">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ formatDubaiDate($order->created_at, 'M d, Y') }}</span>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="mt-4 flex space-x-2">
                                            <a href="{{ route('supplier.orders.show', $order) }}" 
                                               class="flex-1 text-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $color }}-500">
                                                View Details
                                            </a>
                                            @if($order->requires_quotation && in_array($order->status, ['awaiting_quotations', 'pending']))
                                                <a href="{{ route('supplier.orders.quotation', $order) }}" 
                                                   class="flex-1 text-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-{{ $color }}-600 hover:bg-{{ $color }}-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $color }}-500">
                                                    Submit Quote
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="text-{{ $color }}-400 mb-2">
                                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-medium text-{{ $color }}-900">No orders in {{ strtolower($title) }}</h3>
                                <p class="text-xs text-{{ $color }}-600">Orders will appear here as they progress</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('pipelineContainer');
    const leftBtn = document.getElementById('scrollLeftBtn');
    const rightBtn = document.getElementById('scrollRightBtn');

    function updateScrollButtons() {
        const scrollLeft = container.scrollLeft;
        const scrollWidth = container.scrollWidth;
        const clientWidth = container.clientWidth;

        leftBtn.style.opacity = scrollLeft > 0 ? '1' : '0';
        rightBtn.style.opacity = scrollLeft < scrollWidth - clientWidth ? '1' : '0';
    }

    container.addEventListener('scroll', updateScrollButtons);
    updateScrollButtons();

    leftBtn.addEventListener('click', () => {
        container.scrollBy({ left: -320, behavior: 'smooth' });
    });

    rightBtn.addEventListener('click', () => {
        container.scrollBy({ left: 320, behavior: 'smooth' });
    });

    // Order card click handlers
    document.querySelectorAll('.order-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('a')) return; // Don't trigger if clicking a link
            
            const orderId = this.dataset.orderId;
            window.location.href = `/supplier/orders/${orderId}`;
        });
    });
});
</script>
@endpush 