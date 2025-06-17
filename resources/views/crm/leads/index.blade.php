@extends('layouts.crm')

@section('title', 'CRM Leads Pipeline')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header with View Toggle -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Sales Pipeline</h1>
            <p class="text-gray-600">Track leads through your sales process</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="bg-gray-100 p-1 rounded-lg">
                <a href="{{ route('crm.leads.index', array_merge(request()->query(), ['view' => 'pipeline'])) }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (!isset($viewType) || $viewType === 'pipeline') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 17h6m0 0v-6m0 6h2a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 7h6"></path>
                    </svg>
                    Pipeline
                </a>
                <a href="{{ route('crm.leads.index', array_merge(request()->query(), ['view' => 'table'])) }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md {{ (isset($viewType) && $viewType === 'table') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0V4a2 2 0 012-2h14a2 2 0 012 2v16a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                    </svg>
                    Table
                </a>
            </div>
            <a href="{{ route('crm.leads.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Lead
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="hidden" name="view" value="{{ $viewType ?? 'pipeline' }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, email, company..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Source</label>
                    <select name="source" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Sources</option>
                        <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                        <option value="linkedin" {{ request('source') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="email" {{ request('source') == 'email' ? 'selected' : '' }}>Email</option>
                        <option value="phone" {{ request('source') == 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="referral" {{ request('source') == 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="trade_show" {{ request('source') == 'trade_show' ? 'selected' : '' }}>Trade Show</option>
                        <option value="google_ads" {{ request('source') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Priorities</option>
                        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <select name="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
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
        @include('crm.leads.partials.pipeline-view')
    @else
        @include('crm.leads.partials.table-view')
    @endif
</div>

<!-- Lead Quick Actions Modal -->
<div id="leadModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
                <button type="button" onclick="closeLeadModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Pipeline specific styles */
.lead-card {
    transition: all 0.3s ease;
}

.lead-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.lead-card[draggable="true"] {
    cursor: grab;
}

.lead-card[draggable="true"]:active {
    cursor: grabbing;
}

.drop-zone {
    transition: background-color 0.2s ease;
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
.status-new { @apply bg-blue-100 text-blue-800; }
.status-contacted { @apply bg-yellow-100 text-yellow-800; }
.status-qualified { @apply bg-purple-100 text-purple-800; }
.status-proposal { @apply bg-orange-100 text-orange-800; }
.status-negotiation { @apply bg-indigo-100 text-indigo-800; }
.status-won { @apply bg-green-100 text-green-800; }
.status-lost { @apply bg-red-100 text-red-800; }

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
</style>

<script>
// Pipeline interaction functions
function openLeadModal(leadId, leadName) {
    const modal = document.getElementById('leadModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.innerHTML = `
        <div class="space-y-3">
            <p class="text-sm text-gray-600">Actions for: <strong>${leadName}</strong></p>
            <div class="grid grid-cols-2 gap-2">
                <a href="/crm/leads/${leadId}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Details
                </a>
                <a href="/crm/leads/${leadId}/edit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Lead
                </a>
                <button onclick="callLead('${leadId}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    Call
                </button>
                <button onclick="emailLead('${leadId}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Email
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeLeadModal() {
    document.getElementById('leadModal').classList.add('hidden');
}

function changeLeadStatus(leadId, newStatus) {
    // Make AJAX call to update the lead status
    fetch(`/crm/leads/${leadId}/status`, {
        method: 'PATCH',
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
            showNotification('Lead status updated successfully!', 'success');
            // Reload the page to reflect changes
            window.location.reload();
        } else {
            showNotification('Error updating lead status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating lead status', 'error');
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

function callLead(leadId) {
    console.log(`Initiating call for lead ${leadId}`);
    closeLeadModal();
    // Implement call functionality
}

function emailLead(leadId) {
    console.log(`Composing email for lead ${leadId}`);
    closeLeadModal();
    // Implement email functionality
}

// Drag and Drop functionality
let draggedLead = null;
let autoScrollInterval = null;
let pipelineContainer = null;

function handleDragStart(e) {
    draggedLead = e.target;
    e.target.style.opacity = '0.5';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.outerHTML);
    e.dataTransfer.setData('text/plain', e.target.getAttribute('data-lead-id'));
    
    // Get the pipeline container for auto-scrolling
    pipelineContainer = document.querySelector('.pipeline-scroll-container');
    
    // Start monitoring mouse position for auto-scroll
    document.addEventListener('dragover', handleAutoScroll);
}

function handleDragEnd(e) {
    e.target.style.opacity = '';
    draggedLead = null;
    
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
    if (!pipelineContainer || !draggedLead) return;
    
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
    
    const leadId = e.dataTransfer.getData('text/plain');
    const newStatus = e.target.getAttribute('data-status') || e.target.closest('.drop-zone').getAttribute('data-status');
    const currentStatus = draggedLead.getAttribute('data-current-status');
    
    if (newStatus && newStatus !== currentStatus) {
        // Update lead status
        changeLeadStatus(leadId, newStatus);
    }
    
    return false;
}
</script>
@endsection 