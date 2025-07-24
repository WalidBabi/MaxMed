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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Table
                </a>
            </div>
            
            <!-- Add Lead Button -->
            <a href="{{ route('crm.leads.create') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                New Lead
            </a>
        </div>
    </div>

    <!-- Filters Section (for table view) -->
    @if(isset($viewType) && $viewType === 'table')
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <form method="GET" action="{{ route('crm.leads.index') }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <input type="hidden" name="view" value="table">
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                           placeholder="Name, email, company...">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="proposal" {{ request('status') === 'proposal' ? 'selected' : '' }}>Proposal</option>
                        <option value="negotiation" {{ request('status') === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Won</option>
                        <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700">Source</label>
                    <select name="source" id="source" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Sources</option>
                        <option value="website" {{ request('source') === 'website' ? 'selected' : '' }}>Website</option>
                        <option value="linkedin" {{ request('source') === 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                        <option value="email" {{ request('source') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="phone" {{ request('source') === 'phone' ? 'selected' : '' }}>Phone</option>
                        <option value="whatsapp" {{ request('source') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="on_site_visit" {{ request('source') === 'on_site_visit' ? 'selected' : '' }}>On-site Visit</option>
                        <option value="referral" {{ request('source') === 'referral' ? 'selected' : '' }}>Referral</option>
                        <option value="trade_show" {{ request('source') === 'trade_show' ? 'selected' : '' }}>Trade Show</option>
                        <option value="google_ads" {{ request('source') === 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                        <option value="other" {{ request('source') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                    <select name="priority" id="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Priorities</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>
                
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assigned To</label>
                    <select name="assigned_to" id="assigned_to" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filter
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Content Area -->
    @if(!isset($viewType) || $viewType === 'pipeline')
        @include('crm.leads.partials.pipeline-view')
    @else
        @include('crm.leads.partials.table-view')
    @endif
</div>

<!-- Enhanced Lead Action Modal -->
<div id="leadModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeLeadModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Lead Actions
                            </h3>
                            <button type="button" onclick="closeLeadModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="modalContent" class="space-y-4">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Modal -->
<div id="bulkActionsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="bulk-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeBulkActionsModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="bulk-modal-title">
                                Bulk Actions
                            </h3>
                            <button type="button" onclick="closeBulkActionsModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div id="bulkModalContent" class="space-y-4">
                            <!-- Content will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Help Modal -->
<div id="shortcutsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="shortcuts-modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeShortcutsModal()"></div>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
            <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="shortcuts-modal-title">
                                Keyboard Shortcuts
                            </h3>
                            <button type="button" onclick="closeShortcutsModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span>Search leads</span>
                                <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">Ctrl + K</kbd>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span>Create new lead</span>
                                <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">Ctrl + N</kbd>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span>Toggle bulk selection</span>
                                <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">Ctrl + A</kbd>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span>Show keyboard shortcuts</span>
                                <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">?</kbd>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span>Close modal</span>
                                <kbd class="px-2 py-1 bg-gray-200 rounded text-xs font-mono">Esc</kbd>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Enhanced Pipeline Styles */
.lead-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
}

.lead-card:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 12px 32px rgba(0,0,0,0.15);
}

.lead-card:hover .card-checkbox {
    opacity: 1;
}

.lead-card.selected {
    ring: 2px;
    ring-color: rgb(59 130 246);
    transform: translateY(-2px);
}

.lead-card[draggable="true"] {
    cursor: grab;
}

.lead-card[draggable="true"]:active {
    cursor: grabbing;
    transform: rotate(5deg) scale(1.05);
}

.lead-card.dragging {
    opacity: 0.7;
    transform: rotate(5deg) scale(1.1);
    z-index: 1000;
}

.drop-zone {
    transition: all 0.3s ease;
    position: relative;
}

.drop-zone.drag-over {
    background-color: rgba(59, 130, 246, 0.1) !important;
    border: 3px dashed #3b82f6 !important;
    transform: scale(1.02);
}

.drop-zone.drag-over::before {
    content: 'Drop here to move lead';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(59, 130, 246, 0.9);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    z-index: 10;
    pointer-events: none;
}

/* Enhanced Pipeline Scroll */
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
}

.pipeline-scroll-container::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #cbd5e1, #94a3b8);
    border-radius: 6px;
    border: 2px solid #f1f5f9;
}

.pipeline-scroll-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(90deg, #94a3b8, #64748b);
}

/* Auto-scroll indicators */
.pipeline-scroll-container::before,
.pipeline-scroll-container::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: 60px;
    pointer-events: none;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.pipeline-scroll-container::before {
    left: 0;
    background: linear-gradient(to right, rgba(59, 130, 246, 0.2), transparent);
}

.pipeline-scroll-container::after {
    right: 0;
    background: linear-gradient(to left, rgba(59, 130, 246, 0.2), transparent);
}

.pipeline-scroll-container.scroll-left-active::before,
.pipeline-scroll-container.scroll-right-active::after {
    opacity: 1;
}

/* Statistics animations */
@keyframes countUp {
    from { 
        opacity: 0; 
        transform: translateY(20px) scale(0.8); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
    }
    50% { 
        transform: scale(1.05); 
    }
}

.metric-card {
    animation: countUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.metric-card:hover {
    animation: pulse 2s infinite;
}

/* Loading states */
.lead-card.loading {
    opacity: 0.6;
    pointer-events: none;
}

.lead-card.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Status badge colors with enhanced styling */
.status-new { 
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.status-contacted { 
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.status-qualified { 
    background: linear-gradient(135deg, #e9d5ff, #d8b4fe);
    color: #7c2d12;
    border: 1px solid #a855f7;
}

.status-proposal { 
    background: linear-gradient(135deg, #fed7aa, #fdba74);
    color: #9a3412;
    border: 1px solid #f97316;
}

.status-negotiation { 
    background: linear-gradient(135deg, #c7d2fe, #a5b4fc);
    color: #3730a3;
    border: 1px solid #6366f1;
}

.status-won { 
    background: linear-gradient(135deg, #bbf7d0, #86efac);
    color: #14532d;
    border: 1px solid #22c55e;
}

.status-lost { 
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    color: #7f1d1d;
    border: 1px solid #ef4444;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .pipeline-scroll-container {
        flex-direction: column;
        overflow-x: visible;
    }
    
    .pipeline-column {
        width: 100% !important;
        margin-bottom: 1rem;
        min-height: 400px;
    }
    
    .lead-card:hover {
        transform: translateY(-2px) scale(1.01);
    }
    
    .metric-card {
        padding: 1rem;
    }
}

/* Focus styles for accessibility */
.lead-card:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

button:focus, 
input:focus, 
select:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Notification styles */
.notification {
    transform: translateX(100%);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification.show {
    transform: translateX(0);
}

.notification.hide {
    transform: translateX(100%);
    opacity: 0;
}
</style>

<script>
// Enhanced Pipeline JavaScript with Advanced Features and Smooth Navigation

// Global variables
let draggedLead = null;
let autoScrollInterval = null;
let pipelineContainer = null;
let selectedLeads = new Set();
let currentFilters = {
    search: '',
    priority: '',
    source: '',
    overdue: false
};

// Initialize enhanced pipeline functionality
document.addEventListener('DOMContentLoaded', function() {
    initializePipeline();
    initializeKeyboardShortcuts();
    initializeTooltips();
    initializeNavigationArrows();
    
    // Add click event listeners to lead cards
    document.querySelectorAll('.lead-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.card-checkbox') && !e.target.closest('button')) {
                const leadId = this.dataset.leadId;
                const leadName = this.dataset.leadFullName || this.dataset.leadName;
                openEnhancedLeadModal(leadId, leadName);
            }
        });
    });
});

// Initialize pipeline functionality
function initializePipeline() {
    pipelineContainer = document.querySelector('.pipeline-scroll-container');
    
    // Initialize search functionality
    const searchInput = document.getElementById('pipeline-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterPipeline, 300));
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });
    }
    
    // Initialize scroll event listener for arrow visibility
    if (pipelineContainer) {
        pipelineContainer.addEventListener('scroll', updateNavigationArrows);
        // Initial arrow state
        updateNavigationArrows();
    }
    
    console.log('Enhanced pipeline initialized successfully');
}

// Initialize navigation arrows
function initializeNavigationArrows() {
    const leftBtn = document.getElementById('scroll-left-btn');
    const rightBtn = document.getElementById('scroll-right-btn');
    
    if (leftBtn) {
        leftBtn.addEventListener('click', scrollPipelineLeft);
    }
    if (rightBtn) {
        rightBtn.addEventListener('click', scrollPipelineRight);
    }
    
    // Check if arrows are needed on load
    setTimeout(updateNavigationArrows, 100);
}

// Enhanced smooth scroll functions with better easing
function scrollPipelineLeft() {
    if (pipelineContainer) {
        const scrollAmount = 340; // Slightly increased for faster navigation
        
        // Add loading state for visual feedback
        const leftBtn = document.getElementById('scroll-left-btn');
        if (leftBtn) {
            leftBtn.classList.add('loading');
            setTimeout(() => leftBtn.classList.remove('loading'), 400);
        }
        
        pipelineContainer.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
        
        // Update arrow visibility after scroll completes
        setTimeout(() => {
            updateNavigationArrows();
        }, 400);
    }
}

function scrollPipelineRight() {
    if (pipelineContainer) {
        const scrollAmount = 340; // Slightly increased for faster navigation
        
        // Add loading state for visual feedback
        const rightBtn = document.getElementById('scroll-right-btn');
        if (rightBtn) {
            rightBtn.classList.add('loading');
            setTimeout(() => rightBtn.classList.remove('loading'), 400);
        }
        
        pipelineContainer.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
        
        // Update arrow visibility after scroll completes
        setTimeout(() => {
            updateNavigationArrows();
        }, 400);
    }
}

// Optimized navigation arrow update with throttling
let arrowUpdateThrottle = null;
function updateNavigationArrows() {
    if (!pipelineContainer) return;
    
    // Throttle updates for better performance
    if (arrowUpdateThrottle) return;
    arrowUpdateThrottle = setTimeout(() => {
        arrowUpdateThrottle = null;
        
        const leftBtn = document.getElementById('scroll-left-btn');
        const rightBtn = document.getElementById('scroll-right-btn');
        
        if (!leftBtn || !rightBtn) return;
        
        const { scrollLeft, scrollWidth, clientWidth } = pipelineContainer;
        const maxScrollLeft = scrollWidth - clientWidth;
        
        // Show/hide left arrow with smooth transition
        if (scrollLeft > 10) {
            if (leftBtn.style.opacity !== '1') {
                leftBtn.style.opacity = '1';
                leftBtn.style.pointerEvents = 'auto';
                leftBtn.classList.add('hover:scale-110');
            }
        } else {
            if (leftBtn.style.opacity !== '0') {
                leftBtn.style.opacity = '0';
                leftBtn.style.pointerEvents = 'none';
                leftBtn.classList.remove('hover:scale-110');
            }
        }
        
        // Show/hide right arrow with smooth transition
        if (scrollLeft < maxScrollLeft - 10) {
            if (rightBtn.style.opacity !== '1') {
                rightBtn.style.opacity = '1';
                rightBtn.style.pointerEvents = 'auto';
                rightBtn.classList.add('hover:scale-110');
            }
        } else {
            if (rightBtn.style.opacity !== '0') {
                rightBtn.style.opacity = '0';
                rightBtn.style.pointerEvents = 'none';
                rightBtn.classList.remove('hover:scale-110');
            }
        }
    }, 50); // Throttle to maximum 20 updates per second
}

// Enhanced filtering functionality
function filterPipeline() {
    const searchTerm = document.getElementById('pipeline-search')?.value.toLowerCase() || '';
    const leadCards = document.querySelectorAll('.lead-card');
    let visibleCount = 0;
    
    // Update search term
    currentFilters.search = searchTerm;
    
    // Show/hide clear search button
    const clearButton = document.getElementById('clear-search');
    if (clearButton) {
        clearButton.style.opacity = searchTerm ? '1' : '0';
    }
    
    leadCards.forEach(card => {
        const leadName = (card.dataset.leadName || '').toLowerCase();
        const leadCompany = (card.dataset.leadCompany || '').toLowerCase();
        const leadEmail = (card.dataset.leadEmail || '').toLowerCase();
        
        const matchesSearch = !searchTerm || 
            leadName.includes(searchTerm) || 
            leadCompany.includes(searchTerm) || 
            leadEmail.includes(searchTerm);
        
        const matchesPriority = !currentFilters.priority || 
            card.dataset.leadPriority === currentFilters.priority;
        
        const matchesSource = !currentFilters.source || 
            card.dataset.leadSource === currentFilters.source;
        
        const matchesOverdue = !currentFilters.overdue || 
            card.dataset.leadOverdue === 'true';
        
        const shouldShow = matchesSearch && matchesPriority && matchesSource && matchesOverdue;
        
        if (shouldShow) {
            card.style.display = 'block';
            card.classList.add('fade-in');
            visibleCount++;
        } else {
            card.style.display = 'none';
            card.classList.remove('fade-in');
        }
    });
    
    // Update stage counts
    updateStageCounts();
    
    // Show no results message if needed
    toggleNoResultsMessage(visibleCount === 0 && searchTerm);
    
    // Update arrow visibility as content might have changed
    setTimeout(updateNavigationArrows, 100);
    
    console.log(`Filtered pipeline: ${visibleCount} leads visible`);
}

// Clear search functionality
function clearSearch() {
    const searchInput = document.getElementById('pipeline-search');
    if (searchInput) {
        searchInput.value = '';
        filterPipeline();
        searchInput.focus();
    }
}

// Filter toggle functions
function toggleFilter(filterType) {
    console.log(`Toggling filter: ${filterType}`);
    // This would open a dropdown or modal for filter selection
}

function showOverdueOnly() {
    currentFilters.overdue = !currentFilters.overdue;
    filterPipeline();
    
    // Update button appearance
    const button = event.target.closest('button');
    if (currentFilters.overdue) {
        button.classList.add('bg-orange-100', 'text-orange-800', 'border-orange-300');
        button.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
    } else {
        button.classList.remove('bg-orange-100', 'text-orange-800', 'border-orange-300');
        button.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    }
}

function clearAllFilters() {
    currentFilters = {
        search: '',
        priority: '',
        source: '',
        overdue: false
    };
    
    const searchInput = document.getElementById('pipeline-search');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset all filter buttons
    document.querySelectorAll('[onclick*="toggleFilter"], [onclick*="showOverdueOnly"]').forEach(button => {
        button.classList.remove('bg-orange-100', 'text-orange-800', 'border-orange-300');
        button.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    });
    
    filterPipeline();
    showNotification('All filters cleared', 'info');
}

// Enhanced drag and drop functionality with smooth animations
function handleDragStart(e) {
    console.log('Drag start event triggered', e);
    
    draggedLead = e.target;
    e.target.classList.add('dragging');
    e.target.style.opacity = '0.7';
    
    // Add a smooth rotation and scale effect
    e.target.style.transform = 'rotate(5deg) scale(1.05)';
    e.target.style.zIndex = '1000';
    
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.target.outerHTML);
    e.dataTransfer.setData('text/plain', e.target.getAttribute('data-lead-id'));
    
    console.log('Drag data set:', {
        leadId: e.target.getAttribute('data-lead-id'),
        currentStatus: e.target.getAttribute('data-current-status'),
        effectAllowed: e.dataTransfer.effectAllowed
    });
    
    // Get the pipeline container for auto-scrolling
    pipelineContainer = document.querySelector('.pipeline-scroll-container');
    
    // Start monitoring mouse position for auto-scroll with optimized event handling
    document.addEventListener('dragover', handleAutoScroll, { passive: true });
    
    // Add visual feedback to drop zones with improved performance
    requestAnimationFrame(() => {
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.classList.add('drop-zone-active');
            zone.style.transition = 'all 0.3s ease';
        });
    });
    
    // Show navigation arrows during drag
    updateNavigationArrows();
    
    // Add visual indicators for scroll zones
    addScrollZoneIndicators();
}

function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    e.target.style.opacity = '';
    e.target.style.transform = '';
    e.target.style.zIndex = '';
    draggedLead = null;
    
    // Stop auto-scrolling
    stopAutoScroll();
    document.removeEventListener('dragover', handleAutoScroll);
    
    // Remove visual feedback from drop zones
    requestAnimationFrame(() => {
        document.querySelectorAll('.drop-zone').forEach(zone => {
            zone.classList.remove('drop-zone-active', 'drag-over');
            zone.style.transition = '';
        });
    });
    
    // Remove scroll zone indicators
    removeScrollZoneIndicators();
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
        dropZone.classList.add('drag-over');
        dropZone.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';
        dropZone.style.borderColor = '#3b82f6';
        dropZone.style.borderStyle = 'dashed';
        dropZone.style.borderWidth = '2px';
    }
}

function handleDragLeave(e) {
    const dropZone = e.target.closest('.drop-zone');
    if (dropZone && !dropZone.contains(e.relatedTarget)) {
        dropZone.classList.remove('drag-over');
        dropZone.style.backgroundColor = '';
        dropZone.style.borderColor = '';
        dropZone.style.borderStyle = '';
        dropZone.style.borderWidth = '';
    }
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    
    console.log('Drop event triggered', e);
    
    const dropZone = e.target.closest('.drop-zone');
    if (dropZone) {
        dropZone.classList.remove('drag-over');
        dropZone.style.backgroundColor = '';
        dropZone.style.borderColor = '';
        dropZone.style.borderStyle = '';
        dropZone.style.borderWidth = '';
    }
    
    const leadId = e.dataTransfer.getData('text/plain');
    const newStatus = dropZone?.getAttribute('data-status');
    const currentStatus = draggedLead?.getAttribute('data-current-status');
    
    console.log('Drop data:', { leadId, newStatus, currentStatus, draggedLead: !!draggedLead });
    
    if (newStatus && newStatus !== currentStatus && leadId) {
        console.log('Valid drop detected, updating status and refreshing kanban...');
        
        // Show loading indicator
        if (draggedLead) {
            draggedLead.classList.add('loading');
            draggedLead.style.opacity = '0.5';
        }
        
        // Update lead status and refresh kanban only
        updateLeadStatusAndRefreshKanban(leadId, newStatus);
    } else {
        console.log('Invalid drop conditions:', { 
            hasNewStatus: !!newStatus, 
            statusChanged: newStatus !== currentStatus, 
            hasLeadId: !!leadId 
        });
    }
    
    return false;
}

// Function to update lead status and refresh only the kanban board
function updateLeadStatusAndRefreshKanban(leadId, newStatus) {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('Security token missing. Please refresh the page.', 'error');
        return;
    }
    
    console.log('Updating lead status via AJAX...');
    
    // Make AJAX call to update lead status
    fetch(`/crm/leads/${leadId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        console.log('Response received:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            console.log('Lead status updated successfully, refreshing kanban...');
            showNotification(`Lead moved to ${newStatus} stage`, 'success');
            
            // Refresh only the kanban board
            refreshKanbanBoard();
        } else {
            console.error('Server returned success: false');
            showNotification(data.message || 'Failed to update lead status', 'error');
            // Remove loading state
            if (draggedLead) {
                draggedLead.classList.remove('loading');
                draggedLead.style.opacity = '';
            }
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        showNotification(`Error updating lead status: ${error.message}`, 'error');
        // Remove loading state
        if (draggedLead) {
            draggedLead.classList.remove('loading');
            draggedLead.style.opacity = '';
        }
    });
}

// Function to refresh only the kanban board content
function refreshKanbanBoard() {
    console.log('Refreshing kanban board...');
    
    // Get current filters and search
    const searchTerm = document.getElementById('pipeline-search')?.value || '';
    
    // Build URL with current filters
    const url = new URL(window.location.href);
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    }
    
    // Add a parameter to indicate we want only the kanban content
    url.searchParams.set('kanban_only', '1');
    
    fetch(url.toString(), {
        method: 'GET',
        headers: {
            'Accept': 'text/html',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(html => {
        console.log('Kanban board refreshed successfully');
        
        // Parse the response and extract the kanban content
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newKanbanContent = doc.querySelector('#pipeline-container');
        
        if (newKanbanContent) {
            // Replace the current kanban container with the new content
            const currentContainer = document.getElementById('pipeline-container');
            if (currentContainer) {
                currentContainer.innerHTML = newKanbanContent.innerHTML;
                
                // Re-initialize event listeners for the new content
                initializeKanbanEventListeners();
                
                // Update navigation arrows
                updateNavigationArrows();
                
                console.log('Kanban board content updated successfully');
            }
        } else {
            console.error('Could not find kanban content in response');
            showNotification('Error refreshing kanban board', 'error');
        }
    })
    .catch(error => {
        console.error('Error refreshing kanban board:', error);
        showNotification('Error refreshing kanban board', 'error');
    });
}

// Function to re-initialize event listeners after kanban refresh
function initializeKanbanEventListeners() {
    // Re-add click event listeners to lead cards
    document.querySelectorAll('.lead-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.card-checkbox') && !e.target.closest('button')) {
                const leadId = this.dataset.leadId;
                const leadName = this.dataset.leadFullName || this.dataset.leadName;
                openEnhancedLeadModal(leadId, leadName);
            }
        });
    });
    
    console.log('Kanban event listeners re-initialized');
}

// Enhanced auto-scroll functionality with improved performance
function handleAutoScroll(e) {
    if (!pipelineContainer || !draggedLead) return;
    
    const containerRect = pipelineContainer.getBoundingClientRect();
    const mouseX = e.clientX;
    const scrollThreshold = 60; // Reduced threshold for earlier triggering
    const maxScrollSpeed = 25; // Increased max scroll speed
    const minScrollSpeed = 8;  // Minimum scroll speed
    
    // Calculate distance from edges
    const leftDistance = mouseX - containerRect.left;
    const rightDistance = containerRect.right - mouseX;
    
    // Left edge scrolling with variable speed
    if (leftDistance < scrollThreshold && leftDistance >= 0) {
        if (!autoScrollInterval) {
            pipelineContainer.classList.add('scroll-left-active');
            
            // Calculate scroll speed based on proximity to edge (closer = faster)
            const speedMultiplier = 1 - (leftDistance / scrollThreshold);
            const scrollSpeed = minScrollSpeed + (maxScrollSpeed - minScrollSpeed) * speedMultiplier;
            
            let lastUpdate = 0;
            autoScrollInterval = setInterval(() => {
                const now = Date.now();
                if (now - lastUpdate >= 8) { // Throttle to ~120fps max
                    pipelineContainer.scrollLeft -= Math.round(scrollSpeed);
                    lastUpdate = now;
                }
            }, 8); // Reduced interval for smoother scrolling
            
            // Update arrows less frequently for better performance
            setTimeout(() => {
                if (autoScrollInterval) updateNavigationArrows();
            }, 100);
        }
    }
    // Right edge scrolling with variable speed
    else if (rightDistance < scrollThreshold && rightDistance >= 0) {
        if (!autoScrollInterval) {
            pipelineContainer.classList.add('scroll-right-active');
            
            // Calculate scroll speed based on proximity to edge (closer = faster)
            const speedMultiplier = 1 - (rightDistance / scrollThreshold);
            const scrollSpeed = minScrollSpeed + (maxScrollSpeed - minScrollSpeed) * speedMultiplier;
            
            let lastUpdate = 0;
            autoScrollInterval = setInterval(() => {
                const now = Date.now();
                if (now - lastUpdate >= 8) { // Throttle to ~120fps max
                    pipelineContainer.scrollLeft += Math.round(scrollSpeed);
                    lastUpdate = now;
                }
            }, 8); // Reduced interval for smoother scrolling
            
            // Update arrows less frequently for better performance
            setTimeout(() => {
                if (autoScrollInterval) updateNavigationArrows();
            }, 100);
        }
    }
    // Stop scrolling when mouse moves away from edges
    else {
        stopAutoScroll();
    }
}

function stopAutoScroll() {
    if (autoScrollInterval) {
        clearInterval(autoScrollInterval);
        autoScrollInterval = null;
        
        // Update arrow visibility after stopping
        requestAnimationFrame(() => {
            updateNavigationArrows();
        });
    }
    
    if (pipelineContainer) {
        pipelineContainer.classList.remove('scroll-left-active', 'scroll-right-active');
    }
}

// Enhanced lead status update with smooth optimistic UI
function changeLeadStatusOptimistic(leadId, newStatus, oldStatus) {
    console.log('changeLeadStatusOptimistic called:', { leadId, newStatus, oldStatus });
    
    // Optimistic update - move the card immediately with smooth animation
    const leadCard = document.querySelector(`[data-lead-id="${leadId}"]`);
    const oldColumn = document.querySelector(`[data-status="${oldStatus}"] .space-y-4`);
    const newColumn = document.querySelector(`[data-status="${newStatus}"] .space-y-4`);
    
    console.log('DOM elements found:', { leadCard: !!leadCard, oldColumn: !!oldColumn, newColumn: !!newColumn });
    
    if (leadCard && newColumn) {
        // Add smooth movement animation
        leadCard.style.transition = 'all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        leadCard.style.transform = 'scale(1.05)';
        
        setTimeout(() => {
            // Update the card's status attribute
            leadCard.setAttribute('data-current-status', newStatus);
            
            // Move the card to new column with fade effect
            leadCard.style.opacity = '0.7';
            setTimeout(() => {
                newColumn.appendChild(leadCard);
                leadCard.style.opacity = '1';
                leadCard.style.transform = 'scale(1)';
                leadCard.style.transition = '';
                
                // Add a success pulse effect
                leadCard.classList.add('pulse-success');
                setTimeout(() => {
                    leadCard.classList.remove('pulse-success');
                }, 1000);
                
            }, 200);
            
            // Update stage counts immediately
            updateStageCounts();
            
            // Show success feedback
            showNotification(`Lead moved to ${newStatus} stage`, 'success');
        }, 100);
    }
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        showNotification('Security token missing. Please refresh the page.', 'error');
        return;
    }
    
    console.log('Making AJAX call to update lead status...');
    
    // Make the actual API call
    fetch(`/crm/leads/${leadId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(response => {
        console.log('Response received:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            // Remove loading state
            if (leadCard) {
                leadCard.classList.remove('loading');
            }
            console.log('Lead status updated successfully');
        } else {
            // Revert optimistic update on failure
            console.error('Server returned success: false');
            revertLeadStatusUpdate(leadId, oldStatus, newStatus);
            showNotification(data.message || 'Failed to update lead status', 'error');
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        // Revert optimistic update on error
        revertLeadStatusUpdate(leadId, oldStatus, newStatus);
        showNotification(`Error updating lead status: ${error.message}`, 'error');
    });
}

function revertLeadStatusUpdate(leadId, oldStatus, newStatus) {
    const leadCard = document.querySelector(`[data-lead-id="${leadId}"]`);
    const oldColumn = document.querySelector(`[data-status="${oldStatus}"] .space-y-4`);
    
    if (leadCard && oldColumn) {
        // Smooth revert animation
        leadCard.style.transition = 'all 0.4s ease-in-out';
        leadCard.style.transform = 'scale(0.95)';
        leadCard.style.opacity = '0.7';
        
        setTimeout(() => {
            leadCard.setAttribute('data-current-status', oldStatus);
            oldColumn.appendChild(leadCard);
            leadCard.style.opacity = '1';
            leadCard.style.transform = 'scale(1)';
            leadCard.style.transition = '';
            leadCard.classList.remove('loading');
            
            // Add error shake effect
            leadCard.classList.add('shake-error');
            setTimeout(() => {
                leadCard.classList.remove('shake-error');
            }, 500);
            
            updateStageCounts();
        }, 200);
    }
}

// Enhanced modal functionality
function openEnhancedLeadModal(leadId, leadName) {
    const modal = document.getElementById('leadModal');
    const modalContent = document.getElementById('modalContent');
    
    modalContent.innerHTML = `
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-4">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">${leadName}</h4>
                    <p class="text-sm text-gray-600">Choose an action to perform</p>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <a href="/crm/leads/${leadId}" 
               class="group inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Details
            </a>
            <a href="/crm/leads/${leadId}/edit" 
               class="group inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Lead
            </a>
            <button onclick="callLead('${leadId}')" 
                    class="group inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                Call Lead
            </button>
            <button onclick="emailLead('${leadId}')" 
                    class="group inline-flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Send Email
            </button>
        </div>
        
        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
            <h5 class="text-sm font-semibold text-gray-700 mb-2">Quick Status Change</h5>
            <div class="flex flex-wrap gap-2">
                <button onclick="quickStatusChange('${leadId}', 'contacted')" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full hover:bg-yellow-200 transition-all transform hover:scale-105">
                    Mark Contacted
                </button>
                <button onclick="quickStatusChange('${leadId}', 'qualified')" class="px-3 py-1 text-xs bg-purple-100 text-purple-800 rounded-full hover:bg-purple-200 transition-all transform hover:scale-105">
                    Mark Qualified
                </button>
                <button onclick="quickStatusChange('${leadId}', 'proposal')" class="px-3 py-1 text-xs bg-orange-100 text-orange-800 rounded-full hover:bg-orange-200 transition-all transform hover:scale-105">
                    Send Proposal
                </button>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    
    // Focus management for accessibility
    const firstFocusableElement = modal.querySelector('a, button');
    if (firstFocusableElement) {
        firstFocusableElement.focus();
    }
}

function closeLeadModal() {
    const modal = document.getElementById('leadModal');
    modal.classList.add('hidden');
}

// Quick status change function with smooth animation
function quickStatusChange(leadId, newStatus) {
    const leadCard = document.querySelector(`[data-lead-id="${leadId}"]`);
    const currentStatus = leadCard?.getAttribute('data-current-status');
    
    if (currentStatus !== newStatus) {
        changeLeadStatusOptimistic(leadId, newStatus, currentStatus);
        closeLeadModal();
    }
}

// Bulk actions functionality
function toggleCardSelection(leadId, checkbox) {
    if (checkbox.checked) {
        selectedLeads.add(leadId);
    } else {
        selectedLeads.delete(leadId);
    }
    
    // Update card appearance
    const card = document.querySelector(`[data-lead-id="${leadId}"]`);
    if (card) {
        card.classList.toggle('selected', checkbox.checked);
    }
    
    // Update bulk actions button
    updateBulkActionsButton();
}

function updateBulkActionsButton() {
    const button = document.getElementById('bulk-actions-btn');
    const countSpan = document.getElementById('selected-count');
    
    if (button && countSpan) {
        countSpan.textContent = selectedLeads.size;
        
        if (selectedLeads.size > 0) {
            button.disabled = false;
            button.classList.remove('opacity-50', 'cursor-not-allowed');
            button.classList.add('hover:bg-gray-50');
        } else {
            button.disabled = true;
            button.classList.add('opacity-50', 'cursor-not-allowed');
            button.classList.remove('hover:bg-gray-50');
        }
    }
}

function toggleBulkActions() {
    if (selectedLeads.size === 0) return;
    
    const modal = document.getElementById('bulkActionsModal');
    const modalContent = document.getElementById('bulkModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-blue-50 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800">
                <strong>${selectedLeads.size}</strong> lead(s) selected for bulk action
            </p>
        </div>
        
        <div class="space-y-3">
            <button onclick="bulkStatusChange('contacted')" class="w-full text-left px-4 py-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-yellow-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-yellow-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Mark as Contacted</p>
                        <p class="text-sm text-gray-600">Move all selected leads to contacted stage</p>
                    </div>
                </div>
            </button>
            
            <button onclick="bulkStatusChange('qualified')" class="w-full text-left px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-purple-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Mark as Qualified</p>
                        <p class="text-sm text-gray-600">Move all selected leads to qualified stage</p>
                    </div>
                </div>
            </button>
            
            <button onclick="bulkAssign()" class="w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Bulk Assign</p>
                        <p class="text-sm text-gray-600">Assign all selected leads to a team member</p>
                    </div>
                </div>
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeBulkActionsModal() {
    document.getElementById('bulkActionsModal').classList.add('hidden');
}

function bulkStatusChange(newStatus) {
    if (selectedLeads.size === 0) return;
    
    const leadIds = Array.from(selectedLeads);
    
    // Show loading state
    leadIds.forEach(leadId => {
        const card = document.querySelector(`[data-lead-id="${leadId}"]`);
        if (card) {
            card.classList.add('loading');
        }
    });
    
    // Make bulk API call
    fetch('/crm/leads/bulk-status-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            lead_ids: leadIds,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Move cards optimistically
            leadIds.forEach(leadId => {
                const card = document.querySelector(`[data-lead-id="${leadId}"]`);
                const newColumn = document.querySelector(`[data-status="${newStatus}"] .space-y-4`);
                
                if (card && newColumn) {
                    card.setAttribute('data-current-status', newStatus);
                    newColumn.appendChild(card);
                    card.classList.remove('loading', 'selected');
                    
                    // Uncheck checkbox
                    const checkbox = card.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.checked = false;
                    }
                }
            });
            
            // Clear selection
            selectedLeads.clear();
            updateBulkActionsButton();
            updateStageCounts();
            
            closeBulkActionsModal();
            showNotification(`${leadIds.length} leads moved to ${newStatus} stage`, 'success');
        } else {
            showNotification('Failed to update leads', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating leads', 'error');
    })
    .finally(() => {
        // Remove loading state
        leadIds.forEach(leadId => {
            const card = document.querySelector(`[data-lead-id="${leadId}"]`);
            if (card) {
                card.classList.remove('loading');
            }
        });
    });
}

// Column sorting functionality
function toggleColumnMenu(status) {
    const menu = document.getElementById(`column-menu-${status}`);
    if (menu) {
        menu.classList.toggle('hidden');
        
        // Close other menus
        document.querySelectorAll('[id^="column-menu-"]').forEach(otherMenu => {
            if (otherMenu !== menu) {
                otherMenu.classList.add('hidden');
            }
        });
    }
}

function sortColumn(status, sortBy) {
    const column = document.querySelector(`[data-stage="${status}"] .space-y-4`);
    if (!column) return;
    
    const cards = Array.from(column.querySelectorAll('.lead-card'));
    
    cards.sort((a, b) => {
        switch (sortBy) {
            case 'name':
                return (a.dataset.leadName || '').localeCompare(b.dataset.leadName || '');
            case 'value':
                const valueA = parseFloat(a.dataset.leadValue || '0');
                const valueB = parseFloat(b.dataset.leadValue || '0');
                return valueB - valueA;
            case 'priority':
                const priorityOrder = { 'high': 3, 'medium': 2, 'low': 1 };
                return priorityOrder[b.dataset.leadPriority] - priorityOrder[a.dataset.leadPriority];
            case 'date':
                // Sort by creation date (newest first)
                return new Date(b.dataset.createdAt || 0) - new Date(a.dataset.createdAt || 0);
            default:
                return 0;
        }
    });
    
    // Reorder the cards in the DOM
    cards.forEach(card => column.appendChild(card));
    
    // Close the menu
    toggleColumnMenu(status);
    
    showNotification(`${status} column sorted by ${sortBy}`, 'info');
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl + K: Focus search
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.getElementById('pipeline-search');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
        
        // Ctrl + N: New lead
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            window.location.href = '/crm/leads/create';
        }
        
        // Ctrl + A: Toggle all selections
        if (e.ctrlKey && e.key === 'a' && document.activeElement.tagName !== 'INPUT') {
            e.preventDefault();
            toggleAllSelections();
        }
        
        // ?: Show keyboard shortcuts
        if (e.key === '?' && !e.ctrlKey && !e.altKey && document.activeElement.tagName !== 'INPUT') {
            e.preventDefault();
            showShortcutsModal();
        }
        
        // Escape: Close modals
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

function toggleAllSelections() {
    const checkboxes = document.querySelectorAll('.lead-card input[type="checkbox"]');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
        const leadId = checkbox.closest('[data-lead-id]').dataset.leadId;
        toggleCardSelection(leadId, checkbox);
    });
}

function showShortcutsModal() {
    document.getElementById('shortcutsModal').classList.remove('hidden');
}

function closeShortcutsModal() {
    document.getElementById('shortcutsModal').classList.add('hidden');
}

function closeAllModals() {
    closeLeadModal();
    closeBulkActionsModal();
    closeShortcutsModal();
    
    // Close column menus
    document.querySelectorAll('[id^="column-menu-"]').forEach(menu => {
        menu.classList.add('hidden');
    });
}

// Utility functions
function updateStageCounts() {
    document.querySelectorAll('[data-stage]').forEach(stageElement => {
        const stage = stageElement.dataset.stage;
        const countElement = document.querySelector(`[data-stage="${stage}"] .stage-count`);
        const cards = document.querySelectorAll(`[data-status="${stage}"] .lead-card:not([style*="display: none"])`);
        
        if (countElement) {
            countElement.textContent = cards.length;
        }
    });
}

function toggleNoResultsMessage(show) {
    let message = document.getElementById('no-results-message');
    
    if (show && !message) {
        message = document.createElement('div');
        message.id = 'no-results-message';
        message.className = 'col-span-full text-center py-12 bg-gray-50 rounded-lg';
        message.innerHTML = `
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No leads found</h3>
            <p class="text-gray-500">Try adjusting your search terms or filters</p>
        `;
        pipelineContainer?.appendChild(message);
    } else if (!show && message) {
        message.remove();
    }
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(notification => {
        notification.remove();
    });
    
    const notification = document.createElement('div');
    notification.className = `notification fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                ${type === 'success' ? `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                ` : type === 'error' ? `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                ` : `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                `}
            </div>
            <div class="flex-1">
                <p class="font-medium">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Initialize tooltips
function initializeTooltips() {
    // Simple tooltip implementation
    document.querySelectorAll('[title]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            // Implementation for custom tooltips if needed
        });
    });
}

// Debounce utility function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Legacy functions for backward compatibility
function openLeadModal(leadId, leadName) {
    openEnhancedLeadModal(leadId, leadName);
}

function changeLeadStatus(leadId, newStatus) {
    const leadCard = document.querySelector(`[data-lead-id="${leadId}"]`);
    const currentStatus = leadCard?.getAttribute('data-current-status');
    changeLeadStatusOptimistic(leadId, newStatus, currentStatus);
}

function callLead(leadId) {
    console.log(`Initiating call for lead ${leadId}`);
    closeLeadModal();
    showNotification('Call feature would be integrated with your phone system', 'info');
}

function emailLead(leadId) {
    console.log(`Composing email for lead ${leadId}`);
    closeLeadModal();
    showNotification('Email composer would open here', 'info');
}

// Add new CSS animation classes
const additionalStyles = `
<style>
/* Enhanced Pipeline Navigation Arrows */
#scroll-left-btn, #scroll-right-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(156, 163, 175, 0.3);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

#scroll-left-btn:hover, #scroll-right-btn:hover {
    background: rgba(249, 250, 251, 0.98);
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

/* Smooth scroll behavior */
.scroll-smooth {
    scroll-behavior: smooth;
}

/* Enhanced card animations */
.pulse-success {
    animation: pulseSuccess 1s ease-in-out;
}

@keyframes pulseSuccess {
    0%, 100% { 
        transform: scale(1); 
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    50% { 
        transform: scale(1.05); 
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
    }
}

.shake-error {
    animation: shakeError 0.5s ease-in-out;
}

@keyframes shakeError {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Enhanced drop zone indicators */
.drop-zone.drag-over {
    position: relative;
    overflow: visible;
}

.drop-zone.drag-over::before {
    content: '✓ Drop here to move lead';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    z-index: 20;
    pointer-events: none;
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4);
    animation: fadeInScale 0.3s ease-out;
}

@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: translate(-50%, -50%) scale(0.8);
    }
    to {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1);
    }
}

/* Enhanced navigation arrows responsive */
@media (max-width: 768px) {
    #scroll-left-btn, #scroll-right-btn {
        display: none;
    }
}
</style>
`;

// Inject additional styles
document.head.insertAdjacentHTML('beforeend', additionalStyles);

// Add visual indicators for scroll zones during drag
function addScrollZoneIndicators() {
    if (!pipelineContainer) return;
    
    const containerRect = pipelineContainer.getBoundingClientRect();
    
    // Left scroll zone indicator
    const leftIndicator = document.createElement('div');
    leftIndicator.id = 'left-scroll-indicator';
    leftIndicator.className = 'fixed top-0 bottom-0 w-16 bg-gradient-to-r from-blue-500/20 to-transparent pointer-events-none z-40 flex items-center justify-start pl-2';
    leftIndicator.style.left = containerRect.left + 'px';
    leftIndicator.innerHTML = `
        <div class="text-blue-600 animate-pulse">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
            </svg>
        </div>
    `;
    
    // Right scroll zone indicator
    const rightIndicator = document.createElement('div');
    rightIndicator.id = 'right-scroll-indicator';
    rightIndicator.className = 'fixed top-0 bottom-0 w-16 bg-gradient-to-l from-blue-500/20 to-transparent pointer-events-none z-40 flex items-center justify-end pr-2';
    rightIndicator.style.right = (window.innerWidth - containerRect.right) + 'px';
    rightIndicator.innerHTML = `
        <div class="text-blue-600 animate-pulse">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path>
            </svg>
        </div>
    `;
    
    document.body.appendChild(leftIndicator);
    document.body.appendChild(rightIndicator);
}

function removeScrollZoneIndicators() {
    const leftIndicator = document.getElementById('left-scroll-indicator');
    const rightIndicator = document.getElementById('right-scroll-indicator');
    
    if (leftIndicator) leftIndicator.remove();
    if (rightIndicator) rightIndicator.remove();
}

// Console log for debugging
console.log('Enhanced CRM Pipeline with Navigation Arrows loaded successfully');
</script>
@endsection 