<!-- Pipeline View -->
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4">
        <!-- Total Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $counts['all'] }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="mt-1 text-2xl font-semibold text-orange-600">{{ $counts['pending'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Viewed Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Viewed</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $counts['viewed'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quoted Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Quoted</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $counts['quoted'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Accepted Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Accepted</p>
                    <p class="mt-1 text-2xl font-semibold text-indigo-600">{{ $counts['accepted'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Not Available Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Not Available</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $counts['not_available'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Container with Scroll Buttons -->
    <div class="relative">
        <!-- Left Scroll Button -->
        <button id="scrollLeftBtn" class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-opacity duration-200 opacity-0">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>

        <!-- Right Scroll Button -->
        <button id="scrollRightBtn" class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-opacity duration-200 opacity-0">
            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>

        <!-- Pipeline Scroll Container -->
        <div class="pipeline-scroll-container overflow-x-auto flex space-x-6 pb-6" id="pipelineContainer">
            @foreach(['pending' => 'orange', 'viewed' => 'blue', 'quoted' => 'green', 'accepted' => 'indigo', 'not_available' => 'red'] as $status => $color)
                <div class="flex-shrink-0 w-80">
                    <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 rounded-t-lg p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-{{ $color }}-800">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ $counts[$status] ?? 0 }}
                                </span>
                            </h3>
                        </div>
                    </div>
                    <div class="bg-{{ $color }}-25 border-l border-r border-b border-{{ $color }}-200 rounded-b-lg min-h-96 p-4 space-y-4 drop-zone"
                         data-status="{{ $status }}"
                         ondragover="handleDragOver(event)"
                         ondrop="handleDrop(event)"
                         ondragenter="handleDragEnter(event)"
                         ondragleave="handleDragLeave(event)"
                         role="list"
                         aria-label="{{ ucfirst(str_replace('_', ' ', $status)) }} inquiries"
                         tabindex="-1">
                        @if(isset($inquiries[$status]) && $inquiries[$status]->isNotEmpty())
                            @foreach($inquiries[$status] as $inquiry)
                                @php
                                    $response = $inquiry->supplierResponses->where('user_id', auth()->id())->first();
                                    $quotation = $inquiry->quotations->where('supplier_id', auth()->id())->first();
                                    
                                    // Determine the actual status by checking both response and quotation
                                    $actualStatus = 'pending';
                                    if ($quotation) {
                                        if ($quotation->status === 'approved' || $quotation->status === 'accepted') {
                                            $actualStatus = 'accepted';
                                        } else {
                                            $actualStatus = 'quoted';
                                        }
                                    } elseif ($response) {
                                        $actualStatus = $response->status;
                                    }
                                @endphp
                                @if($actualStatus === $status)
                                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md focus-within:shadow-md focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-{{ $color }}-500 transition-all duration-200 inquiry-card"
                                     draggable="true"
                                     data-inquiry-id="{{ $inquiry->id }}"
                                     data-current-status="{{ $actualStatus }}"
                                     ondragstart="handleDragStart(event)"
                                     ondragend="handleDragEnd(event)"
                                     role="listitem"
                                     tabindex="0"
                                     aria-label="{{ $inquiry->customer_name }} - {{ $inquiry->product_name }}">
                                    
                                    <!-- Product Name Header -->
                                    <div class="px-4 py-3 bg-{{ $color }}-50 border-b border-{{ $color }}-100">
                                        <h3 class="text-base font-semibold text-{{ $color }}-900 line-clamp-2">
                                            @if($inquiry->product_id && $inquiry->product)
                                                {{ $inquiry->product->name }}
                                            @else
                                                {{ $inquiry->product_name }}
                                            @endif
                                        </h3>

                                    </div>

                                    <!-- Customer Info -->
                                    <div class="p-4">
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-{{ $color }}-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-{{ $color }}-800">
                                                        {{ strtoupper(substr($inquiry->customer_name ?? '', 0, 2)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $inquiry->customer_name }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ $inquiry->email }}</p>
                                            </div>
                                        </div>
                                    
                                        <div class="mt-3 space-y-3">
                                            @if($inquiry->phone)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    <span>{{ $inquiry->phone }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between items-center text-xs text-gray-500">
                                                <span>{{ $inquiry->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-100">
                                        <div class="flex justify-between">
                                            <button onclick="window.location.href='{{ route('supplier.inquiries.show', $inquiry) }}'"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-{{ $color }}-700 bg-{{ $color }}-100 hover:bg-{{ $color }}-200 focus:bg-{{ $color }}-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $color }}-500"
                                                    aria-label="View {{ $inquiry->customer_name }}'s inquiry">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            <div class="flex flex-col items-center justify-center py-12 text-gray-500" role="status">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <p class="text-sm font-medium">No inquiries in this stage</p>
                                <p class="text-xs text-gray-400 mt-1">Inquiries will appear here when they reach this stage</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* Pipeline specific styles */
.inquiry-card {
    transition: all 0.3s ease;
    cursor: default;
}

.inquiry-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    cursor: grab;
}

.inquiry-card:active {
    cursor: grabbing;
}

.drop-zone {
    transition: background-color 0.2s ease;
}

.drop-zone.bg-opacity-50 {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border: 2px dashed #3b82f6;
}

/* Pipeline scroll container */
.pipeline-scroll-container {
    scroll-behavior: smooth;
    position: relative;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f1f5f9;
}

.pipeline-scroll-container::-webkit-scrollbar {
    height: 12px;
}

.pipeline-scroll-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 6px;
    margin: 0 40px;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 6px;
    border: 2px solid #f1f5f9;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Scroll buttons */
#scrollLeftBtn, #scrollRightBtn {
    transition: all 0.2s ease;
}

#scrollLeftBtn:hover, #scrollRightBtn:hover {
    background-color: #f8fafc;
    transform: scale(1.05);
}

#scrollLeftBtn:active, #scrollRightBtn:active {
    transform: scale(0.95);
}
</style>

<script>
// Pipeline interaction functions
let draggedInquiry = null;
const pipelineContainer = document.getElementById('pipelineContainer');
const leftBtn = document.getElementById('scrollLeftBtn');
const rightBtn = document.getElementById('scrollRightBtn');
const SCROLL_AMOUNT = 320; // Width of one column

// Initialize scroll buttons and drag-drop
document.addEventListener('DOMContentLoaded', function() {
    if (!pipelineContainer || !leftBtn || !rightBtn) {
        console.error('Required elements not found');
        return;
    }

    updateScrollButtons();
    
    // Add scroll event listener
    pipelineContainer.addEventListener('scroll', updateScrollButtons);
    
    // Add click events for scroll buttons
    leftBtn.addEventListener('click', function() {
        scrollPipeline('left');
    });
    
    rightBtn.addEventListener('click', function() {
        scrollPipeline('right');
    });

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        // Only handle keyboard navigation when focus is within the pipeline container
        const activeElement = document.activeElement;
        const isPipelineFocused = pipelineContainer.contains(activeElement) || 
                                 activeElement === leftBtn || 
                                 activeElement === rightBtn;

        if (!isPipelineFocused) return;

        if (e.key === 'ArrowLeft') {
            e.preventDefault(); // Prevent page scroll
            scrollPipeline('left');
        } else if (e.key === 'ArrowRight') {
            e.preventDefault(); // Prevent page scroll
            scrollPipeline('right');
        }
    });

    // Initialize drag and drop and keyboard navigation for cards
    const inquiryCards = document.querySelectorAll('.inquiry-card');
    const dropZones = document.querySelectorAll('.drop-zone');

    if (inquiryCards.length === 0) {
        console.log('No inquiry cards found');
    } else {
        inquiryCards.forEach(function(card) {
            if (!card) return;
            
            // Add drag and drop event listeners
            card.addEventListener('dragstart', handleDragStart);
            card.addEventListener('dragend', handleDragEnd);
            
            // Add keyboard navigation for cards
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const viewButton = card.querySelector('button');
                    if (viewButton) {
                        viewButton.click();
                    }
                } else if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const cards = Array.from(card.closest('.drop-zone').querySelectorAll('.inquiry-card'));
                    const currentIndex = cards.indexOf(card);
                    let nextIndex;
                    
                    if (e.key === 'ArrowUp') {
                        nextIndex = currentIndex > 0 ? currentIndex - 1 : cards.length - 1;
                    } else {
                        nextIndex = currentIndex < cards.length - 1 ? currentIndex + 1 : 0;
                    }
                    
                    cards[nextIndex].focus();
                    ensureCardVisible(cards[nextIndex]);
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                    e.preventDefault();
                    const columns = Array.from(document.querySelectorAll('.drop-zone'));
                    const currentColumn = card.closest('.drop-zone');
                    const currentColumnIndex = columns.indexOf(currentColumn);
                    let nextColumnIndex;
                    
                    if (e.key === 'ArrowLeft') {
                        nextColumnIndex = currentColumnIndex > 0 ? currentColumnIndex - 1 : columns.length - 1;
                    } else {
                        nextColumnIndex = currentColumnIndex < columns.length - 1 ? currentColumnIndex + 1 : 0;
                    }
                    
                    const nextColumn = columns[nextColumnIndex];
                    const nextCards = nextColumn.querySelectorAll('.inquiry-card');
                    if (nextCards.length > 0) {
                        nextCards[0].focus();
                        ensureColumnVisible(nextColumn);
                    } else {
                        nextColumn.focus();
                        ensureColumnVisible(nextColumn);
                    }
                }
            });

            // Add focus management
            card.addEventListener('focus', function() {
                ensureCardVisible(card);
            });
        });
    }

    if (dropZones.length === 0) {
        console.error('No drop zones found');
    } else {
        dropZones.forEach(function(zone) {
            if (!zone) return;
            zone.addEventListener('dragover', handleDragOver);
            zone.addEventListener('dragenter', handleDragEnter);
            zone.addEventListener('dragleave', handleDragLeave);
            zone.addEventListener('drop', handleDrop);
        });
    }
});

function ensureCardVisible(card) {
    if (!card || !pipelineContainer) return;

    const cardRect = card.getBoundingClientRect();
    const containerRect = pipelineContainer.getBoundingClientRect();

    if (cardRect.left < containerRect.left) {
        // Card is off-screen to the left
        scrollPipeline('left');
    } else if (cardRect.right > containerRect.right) {
        // Card is off-screen to the right
        scrollPipeline('right');
    }
}

function ensureColumnVisible(column) {
    if (!column || !pipelineContainer) return;

    const columnRect = column.getBoundingClientRect();
    const containerRect = pipelineContainer.getBoundingClientRect();
    
    if (columnRect.left < containerRect.left) {
        // Column is off-screen to the left
        pipelineContainer.scrollBy({
            left: columnRect.left - containerRect.left - 20,
            behavior: 'smooth'
        });
        setTimeout(updateScrollButtons, 100);
    } else if (columnRect.right > containerRect.right) {
        // Column is off-screen to the right
        pipelineContainer.scrollBy({
            left: columnRect.right - containerRect.right + 20,
            behavior: 'smooth'
        });
        setTimeout(updateScrollButtons, 100);
    }
}

function scrollPipeline(direction) {
    if (!pipelineContainer) return;

    const scrollAmount = direction === 'left' ? -SCROLL_AMOUNT : SCROLL_AMOUNT;
    pipelineContainer.scrollBy({
        left: scrollAmount,
        behavior: 'smooth'
    });
    setTimeout(updateScrollButtons, 100); // Update buttons after scroll animation
}

function updateScrollButtons() {
    if (!pipelineContainer || !leftBtn || !rightBtn) {
        console.error('Required elements not found');
        return;
    }

    const scrollLeft = Math.round(pipelineContainer.scrollLeft);
    const maxScroll = pipelineContainer.scrollWidth - pipelineContainer.clientWidth;
    
    // Show/hide left arrow
    leftBtn.style.opacity = scrollLeft > 0 ? '1' : '0';
    
    // Show/hide right arrow
    rightBtn.style.opacity = scrollLeft < maxScroll ? '1' : '0';
    
    // Enable/disable scroll buttons based on scroll position
    leftBtn.style.pointerEvents = scrollLeft > 0 ? 'auto' : 'none';
    rightBtn.style.pointerEvents = scrollLeft < maxScroll ? 'auto' : 'none';
}

function handleDragStart(e) {
    draggedInquiry = e.target.closest('.inquiry-card');
    if (!draggedInquiry) {
        console.error('No inquiry card found');
        return;
    }

    draggedInquiry.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', draggedInquiry.outerHTML);
    e.dataTransfer.setData('text/plain', draggedInquiry.getAttribute('data-inquiry-id'));
}

function handleDragEnd(e) {
    if (draggedInquiry) {
        draggedInquiry.style.opacity = '';
        draggedInquiry = null;
    }
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    const dropZone = e.target.closest('.drop-zone');
    if (dropZone) {
        dropZone.classList.add('bg-opacity-50');
    }
}

function handleDragLeave(e) {
    const dropZone = e.target.closest('.drop-zone');
    if (dropZone) {
        dropZone.classList.remove('bg-opacity-50');
    }
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    const dropZone = e.target.closest('.drop-zone');
    if (!dropZone || !draggedInquiry) {
        console.error('Drop zone or dragged inquiry not found');
        return false;
    }
    
    dropZone.classList.remove('bg-opacity-50');
    
    const inquiryId = draggedInquiry.getAttribute('data-inquiry-id');
    const newStatus = dropZone.getAttribute('data-status');
    const currentStatus = draggedInquiry.getAttribute('data-current-status');
    
    if (!inquiryId || !newStatus || !currentStatus) {
        console.error('Missing required data for status update');
        alert('Failed to update status. Please try again.');
        return false;
    }
    
    if (newStatus === currentStatus) {
        return false;
    }
    
    updateInquiryStatus(inquiryId, newStatus);
    return false;
}

function updateInquiryStatus(inquiryId, newStatus) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Failed to update status. Please refresh the page and try again.');
        return;
    }

    fetch(`/supplier/inquiries/${inquiryId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || `HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            console.error('Failed to update status:', data.message);
            alert(data.message || 'Failed to update status. Please try again.');
        }
    })
    .catch(function(error) {
        console.error('Error:', error);
        alert(error.message || 'An error occurred. Please try again later.');
    });
}
</script> 