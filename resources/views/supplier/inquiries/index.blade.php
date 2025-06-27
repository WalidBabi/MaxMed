@extends('supplier.layouts.app')

@section('title', 'Inquiries & Quotations')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header with View Toggle -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Product Inquiries Pipeline</h1>
            <p class="text-gray-600">Manage and respond to product inquiries from customers</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="bg-gray-100 p-1 rounded-lg">
                <a href="{{ route('supplier.inquiries.index', array_merge(request()->query(), ['view' => 'pipeline'])) }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (!isset($viewType) || $viewType === 'pipeline') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 17h6m0 0v-6m0 6h2a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 7h6"></path>
                    </svg>
                    Pipeline
                </a>
                <a href="{{ route('supplier.inquiries.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (isset($viewType) && $viewType === 'table') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a2 2 0 012-2h14a2 2 0 012 2v16a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Table
                </a>
            </div>
        </div>
                </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="view" value="{{ $viewType ?? 'pipeline' }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product, reference number..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Response</option>
                        <option value="viewed" {{ request('status') == 'viewed' ? 'selected' : '' }}>Viewed</option>
                        <option value="interested" {{ request('status') == 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quotations Submitted</option>
                        <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="not_available" {{ request('status') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                    <select name="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="quarter" {{ request('date_range') == 'quarter' ? 'selected' : '' }}>This Quarter</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Category</label>
                    <select name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(!isset($viewType) || $viewType === 'pipeline')
        @include('supplier.inquiries.partials.pipeline-view')
    @else
        @include('supplier.inquiries.partials.table-view')
    @endif
                </div>

<!-- Inquiry Quick Actions Modal -->
<div id="inquiryModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Quick Actions
                        </h3>
                        <div class="mt-4 space-y-2" id="modalContent">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeInquiryModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Not Available Modal -->
<div id="notAvailableModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-lg w-full mx-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Mark as Not Available</h3>
            <form id="notAvailableForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea id="reason" name="reason" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeNotAvailableModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
/* Pipeline specific styles */
.inquiry-card {
    transition: all 0.3s ease;
    cursor: move; /* Fallback for older browsers */
    cursor: grab;
    position: relative;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    user-select: none;
    -webkit-user-select: none;
    touch-action: none;
}

.inquiry-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.inquiry-card[draggable="true"] {
    cursor: grab;
}

.inquiry-card[draggable="true"]:active {
    cursor: grabbing;
}

.drop-zone {
    transition: background-color 0.2s ease;
    border-radius: 0.75rem;
    background: rgba(255, 255, 255, 0.5);
    backdrop-filter: blur(8px);
    min-height: 400px;
}

.drop-zone.bg-opacity-50 {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border: 2px dashed #3b82f6;
}

/* Pipeline auto-scroll container */
.pipeline-scroll-container {
    scroll-behavior: smooth;
    position: relative;
}

.pipeline-scroll-container::-webkit-scrollbar {
    height: 8px;
}

.pipeline-scroll-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Auto-scroll edge indicators */
.pipeline-scroll-container::before,
.pipeline-scroll-container::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 100px;
    pointer-events: none;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Scroll zone cursors */
.pipeline-scroll-container.scroll-left-active {
    cursor: w-resize;
}

.pipeline-scroll-container.scroll-right-active {
    cursor: e-resize;
}

.pipeline-scroll-container::before {
    left: 0;
    background: linear-gradient(to right, rgba(59, 130, 246, 0.1), transparent);
}

.pipeline-scroll-container::after {
    right: 0;
    background: linear-gradient(to left, rgba(59, 130, 246, 0.1), transparent);
}

.pipeline-scroll-container.scroll-left-active::before,
.pipeline-scroll-container.scroll-right-active::after {
    opacity: 1;
}

/* Pipeline statistics animations */
@keyframes countUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.metric-card {
    animation: countUp 0.6s ease-out;
}

/* Status badge colors */
.status-pending { @apply bg-yellow-100 text-yellow-800; }
.status-viewed { @apply bg-blue-100 text-blue-800; }
.status-interested { @apply bg-indigo-100 text-indigo-800; }
.status-quoted { @apply bg-purple-100 text-purple-800; }
.status-accepted { @apply bg-green-100 text-green-800; }
.status-not_available { @apply bg-red-100 text-red-800; }
.status-not_interested { @apply bg-red-100 text-red-800; }

/* Column Headers */
.kanban-column-header {
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
    backdrop-filter: blur(8px);
}

/* Mobile responsiveness for pipeline */
@media (max-width: 768px) {
    .pipeline-container {
        flex-direction: column;
    }
    
    .pipeline-column {
        width: 100% !important;
        margin-bottom: 1rem;
    }
}

/* Loading State */
.inquiry-card.opacity-50 {
    position: relative;
    overflow: hidden;
}

.inquiry-card.opacity-50::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.4),
        transparent
    );
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Dragging State */
.inquiry-card.dragging {
    opacity: 0.9;
    cursor: grabbing !important;
    transform: scale(1.02);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endpush

@push('scripts')
<script>
// Pipeline interaction functions
function openInquiryModal(inquiryId, inquiryCustomer) {
    const modal = document.getElementById('inquiryModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.innerHTML = `
        <div class="space-y-3">
            <p class="text-sm text-gray-600">Actions for: <strong>${inquiryCustomer}</strong></p>
            <div class="grid grid-cols-2 gap-2">
                <a href="/supplier/inquiries/${inquiryId}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Details
                </a>
                <a href="/supplier/inquiries/${inquiryId}/edit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Inquiry
                </a>
                <button onclick="respondInquiry('${inquiryId}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Respond
                </a>
                <button onclick="markNotAvailable('${inquiryId}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Not Available
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeInquiryModal() {
    document.getElementById('inquiryModal').classList.add('hidden');
}

function changeInquiryStatus(inquiryId, newStatus) {
    // Make AJAX call to update the inquiry status
    fetch(`/supplier/inquiries/${inquiryId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Inquiry status updated successfully!', 'success');
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            showNotification('Error updating inquiry status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating inquiry status', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-md shadow-lg transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

function respondInquiry(inquiryId) {
    console.log(`Opening response form for inquiry ${inquiryId}`);
    closeInquiryModal();
    // Implement response functionality
}

function markNotAvailable(inquiryId) {
    const modal = document.getElementById('notAvailableModal');
    const form = document.getElementById('notAvailableForm');
    
    form.action = `/supplier/inquiries/${inquiryId}/not-available`;
    modal.classList.remove('hidden');
    closeInquiryModal();
}

function closeNotAvailableModal() {
    const modal = document.getElementById('notAvailableModal');
    modal.classList.add('hidden');
}

// Drag and Drop functionality
let draggedInquiry = null;
let autoScrollInterval = null;
let pipelineContainer = null;

function handleDragStart(e) {
    draggedInquiry = e.target;
    e.target.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.outerHTML);
    e.dataTransfer.setData('text/plain', e.target.getAttribute('data-inquiry-id'));
    
    // Get the pipeline container for auto-scrolling
    pipelineContainer = document.querySelector('.pipeline-scroll-container');
    
    // Start monitoring mouse position for auto-scroll
    document.addEventListener('dragover', handleAutoScroll);
}

function handleDragEnd(e) {
    e.target.style.opacity = '';
        draggedInquiry = null;
    
    // Stop auto-scrolling
    stopAutoScroll();
    document.removeEventListener('dragover', handleAutoScroll);
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

// Auto-scroll functionality for pipeline
function handleAutoScroll(e) {
    if (!pipelineContainer || !draggedInquiry) return;
    
    const containerRect = pipelineContainer.getBoundingClientRect();
    const mouseX = e.clientX;
    const scrollThreshold = 120; // Distance from edge to trigger scroll
    const scrollSpeed = 10; // Scroll speed
    
    // Check if mouse is near left edge
    if (mouseX - containerRect.left < scrollThreshold && pipelineContainer.scrollLeft > 0) {
        pipelineContainer.classList.add('scroll-left-active');
        pipelineContainer.classList.remove('scroll-right-active');
        startAutoScroll(-scrollSpeed);
    }
    // Check if mouse is near right edge
    else if (containerRect.right - mouseX < scrollThreshold) {
        const maxScroll = pipelineContainer.scrollWidth - pipelineContainer.clientWidth;
        if (pipelineContainer.scrollLeft < maxScroll) {
            pipelineContainer.classList.add('scroll-right-active');
            pipelineContainer.classList.remove('scroll-left-active');
            startAutoScroll(scrollSpeed);
        }
    }
    // Stop scrolling if mouse is in the middle area
    else {
        stopAutoScroll();
    }
}

function startAutoScroll(speed) {
    stopAutoScroll(); // Clear any existing interval
    
    autoScrollInterval = setInterval(() => {
        if (!pipelineContainer) return;
        
        const newScrollLeft = pipelineContainer.scrollLeft + speed;
        const maxScroll = pipelineContainer.scrollWidth - pipelineContainer.clientWidth;
        
        // Ensure we don't scroll beyond boundaries
        if (newScrollLeft >= 0 && newScrollLeft <= maxScroll) {
            pipelineContainer.scrollLeft = newScrollLeft;
        } else {
            stopAutoScroll();
        }
    }, 16); // ~60fps for smooth scrolling
}

function stopAutoScroll() {
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
        autoScrollInterval = null;
    }
    
    // Remove visual indicators
    if (pipelineContainer) {
        pipelineContainer.classList.remove('scroll-left-active', 'scroll-right-active');
    }
}

function handleDragEnter(e) {
    e.target.classList.add('bg-opacity-50');
}

function handleDragLeave(e) {
    e.target.classList.remove('bg-opacity-50');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    e.target.classList.remove('bg-opacity-50');
    
    const inquiryId = e.dataTransfer.getData('text/plain');
    const newStatus = e.target.getAttribute('data-status') || e.target.closest('.drop-zone').getAttribute('data-status');
    const currentStatus = draggedInquiry.getAttribute('data-current-status');
    
    if (newStatus && newStatus !== currentStatus) {
        // Update inquiry status
        changeInquiryStatus(inquiryId, newStatus);
    }
    
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    // Handle quotation button clicks
    document.querySelectorAll('.quotation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const inquiryId = this.dataset.inquiryId;
            window.location.href = `/supplier/inquiries/${inquiryId}/quotation`;
        });
    });

    // Handle filter form submission
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        });
    }

    // Initialize drag and drop functionality
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    // Handle status changes in pipeline view
    const dropZones = document.querySelectorAll('.drop-zone');
    if (dropZones) {
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('bg-opacity-50');
            });

            zone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('bg-opacity-50');
            });

            zone.addEventListener('drop', async function(e) {
                e.preventDefault();
                this.classList.remove('bg-opacity-50');

                const inquiryId = e.dataTransfer.getData('text/plain');
                const newStatus = this.dataset.status;
        
        try {
            const response = await fetch(`/supplier/inquiries/${inquiryId}/status`, {
                        method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (!response.ok) {
                        throw new Error('Failed to update status');
                    }

                    // Reload the page to reflect changes
                window.location.reload();
        } catch (error) {
            console.error('Error updating status:', error);
                    alert('Failed to update inquiry status. Please try again.');
                }
            });
        });
    }

    // Make inquiry cards draggable
    const inquiryCards = document.querySelectorAll('.inquiry-card');
    if (inquiryCards) {
        inquiryCards.forEach(card => {
            card.setAttribute('draggable', true);
            
            card.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.dataset.inquiryId);
                this.classList.add('opacity-50');
            });

            card.addEventListener('dragend', function() {
                this.classList.remove('opacity-50');
    });
});
    }
}
</script>
@endpush 