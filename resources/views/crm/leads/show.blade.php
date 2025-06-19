@extends('layouts.crm')

@section('title', 'Lead Details - ' . $lead->full_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $lead->full_name }}</h1>
                    <p class="text-gray-600">{{ $lead->company_name }} • {{ $lead->job_title }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('crm.leads.edit', $lead) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Edit Lead
                    </a>
                    <a href="{{ route('crm.leads.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Back to Leads
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lead Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="mailto:{{ $lead->email }}" class="text-blue-600 hover:text-blue-800">{{ $lead->email }}</a>
                            </p>
                        </div>
                        @if($lead->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <a href="tel:{{ $lead->phone }}" class="text-blue-600 hover:text-blue-800">{{ $lead->phone }}</a>
                            </p>
                        </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Company</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->company_name }}</p>
                        </div>
                        @if($lead->job_title)
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Job Title</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $lead->job_title }}</p>
                        </div>
                        @endif
                    </div>
                    @if($lead->company_address)
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500">Address</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $lead->company_address }}</p>
                    </div>
                    @endif
                </div>

                <!-- Activities -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Activity Timeline</h3>
                            <button onclick="toggleActivityForm()" 
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Activity
                            </button>
                        </div>
                    </div>

                    <!-- Add Activity Form -->
                    <div id="activityForm" class="hidden border-b border-gray-200 p-6">
                        <form action="{{ route('crm.leads.activity.add', $lead) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                                    <select name="type" id="type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="call">Phone Call</option>
                                        <option value="email">Email</option>
                                        <option value="meeting">Meeting</option>
                                        <option value="note">Note</option>
                                        <option value="quote_sent">Quote Sent</option>
                                        <option value="demo">Demo</option>
                                        <option value="follow_up">Follow-up</option>
                                        <option value="task">Task</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="status" id="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="completed">Completed</option>
                                        <option value="scheduled">Scheduled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <input type="text" name="subject" id="subject" required placeholder="Brief description of the activity"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="mt-4">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" id="description" rows="3" placeholder="Detailed notes about the activity"
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label for="activity_date" class="block text-sm font-medium text-gray-700 mb-1">Activity Date</label>
                                    <input type="datetime-local" name="activity_date" id="activity_date" 
                                           value="{{ nowDubai('Y-m-d\TH:i') }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div id="dueDateField" class="hidden">
                                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                                    <input type="datetime-local" name="due_date" id="due_date"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end space-x-3">
                                <button type="button" onclick="toggleActivityForm()" 
                                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                    Add Activity
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Activity List -->
                    <div class="p-6">
                        @if($lead->activities->count() > 0)
                            <div class="space-y-6">
                                @foreach($lead->activities as $activity)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-{{ $activity->type_color }}-100">
                                                <span class="text-{{ $activity->type_color }}-600 text-sm font-medium">
                                                    {{ strtoupper(substr($activity->type, 0, 1)) }}
                                                </span>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">{{ $activity->subject }}</p>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $activity->status_color }}-100 text-{{ $activity->status_color }}-800">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </div>
                                            @if($activity->description)
                                                <p class="mt-1 text-sm text-gray-600">{{ $activity->description }}</p>
                                            @endif
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <span class="font-medium text-{{ $activity->type_color }}-600 mr-2">{{ ucfirst(str_replace('_', ' ', $activity->type)) }}</span>
                                                <span class="mr-2">•</span>
                                                <span>{{ $activity->activity_date ? $activity->activity_date->format('M j, Y g:i A') : 'N/A' }}</span>
                                                <span class="mr-2">•</span>
                                                <span>{{ $activity->user->name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No activities recorded yet. Add your first activity above.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Lead Status -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Status</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->status_color }}-100 text-{{ $lead->status_color }}-800">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Priority</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->priority_color }}-100 text-{{ $lead->priority_color }}-800">
                                {{ ucfirst($lead->priority) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Source</span>
                            <span class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Assigned To</span>
                            <span class="text-sm text-gray-900">{{ $lead->assignedUser->name }}</span>
                        </div>
                        @if($lead->estimated_value)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Est. Value</span>
                            <span class="text-sm font-semibold text-gray-900">AED {{ number_format($lead->estimated_value, 0) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Key Dates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Key Dates</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Created</span>
                                                            <span class="text-sm text-gray-900">{{ $lead->created_at ? $lead->created_at->format('M j, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Last Contact</span>
                            <span class="text-sm text-gray-900">
                                @if($lead->last_contacted_at)
                                    {{ $lead->last_contacted_at ? $lead->last_contacted_at->format('M j, Y') : 'Never' }}
                                    @if($lead->isOverdue())
                                        <span class="text-red-600 ml-1">⚠️</span>
                                    @endif
                                @else
                                    <span class="text-red-600">Never</span>
                                @endif
                            </span>
                        </div>
                        @if($lead->expected_close_date)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Expected Close</span>
                                                            <span class="text-sm text-gray-900">{{ $lead->expected_close_date ? $lead->expected_close_date->format('M j, Y') : 'Not set' }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($lead->notes)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                    <p class="text-sm text-gray-600">{{ $lead->notes }}</p>
                </div>
                @endif

                <!-- Deal Conversion -->
                @if(in_array($lead->status, ['qualified', 'proposal']) && $lead->deals->count() === 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Convert to Deal</h3>
                    <p class="text-sm text-gray-600 mb-4">This qualified lead is ready to be converted to a formal deal.</p>
                    <button onclick="toggleDealForm()" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                        <i class="fas fa-handshake mr-2"></i>Create Deal
                    </button>
                    
                    <!-- Deal Creation Form -->
                    <div id="dealForm" class="hidden mt-4 border-t pt-4">
                        <form action="{{ route('crm.leads.convert-to-deal', $lead) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="deal_name" class="block text-sm font-medium text-gray-700 mb-1">Deal Name</label>
                                    <input type="text" name="deal_name" id="deal_name" required 
                                           value="{{ $lead->company_name }} - Equipment Purchase"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="deal_value" class="block text-sm font-medium text-gray-700 mb-1">Deal Value (AED)</label>
                                        <input type="number" name="deal_value" id="deal_value" required min="0" step="0.01"
                                               value="{{ $lead->estimated_value }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                    <div>
                                        <label for="expected_close_date" class="block text-sm font-medium text-gray-700 mb-1">Expected Close Date</label>
                                        <input type="date" name="expected_close_date" id="expected_close_date" required
                                               value="{{ $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : now()->addDays(30)->format('Y-m-d') }}"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    </div>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deal Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                              placeholder="Describe the deal, products interested, specific requirements...">{{ $lead->notes }}</textarea>
                                </div>
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="toggleDealForm()" 
                                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                        Create Deal
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Supplier Quotation Process -->
                @if(in_array($lead->status, ['contacted', 'qualified']) && !$lead->hasActiveQuotationRequest())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Get Supplier Pricing</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <h4 class="font-medium text-blue-900">Before Creating a Deal</h4>
                                <p class="text-sm text-blue-700 mt-1">To provide accurate pricing, create a quotation request and forward it to suppliers first.</p>
                            </div>
                        </div>
                    </div>
                    
                    <button onclick="toggleQuotationForm()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Create Quotation Request
                    </button>
                    
                    <!-- Quotation Request Form -->
                    <div id="quotationForm" class="hidden mt-4 border-t pt-4">
                        <form action="{{ route('crm.leads.create-quotation-request', $lead) }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="product_search" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                                    <input type="text" id="product_search" placeholder="Search for products..." 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <input type="hidden" name="product_id" id="selected_product_id" required>
                                    <div id="product_results" class="hidden mt-2 border border-gray-300 rounded-md bg-white shadow-sm max-h-48 overflow-y-auto"></div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                                        <input type="number" name="quantity" id="quantity" required min="1" value="1"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Size (if applicable)</label>
                                        <input type="text" name="size" id="size" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="e.g., 100ml, Pack of 10">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements & Specifications</label>
                                    <textarea name="requirements" id="requirements" rows="3"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Specific requirements, certifications needed, delivery timeline..."></textarea>
                                </div>
                                
                                <div>
                                    <label for="internal_notes" class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
                                    <textarea name="internal_notes" id="internal_notes" rows="2"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Internal notes for suppliers...">Lead: {{ $lead->full_name }} ({{ $lead->company_name }})</textarea>
                                </div>
                                
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="toggleQuotationForm()" 
                                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit" 
                                            class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        Create & Forward to Suppliers
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Active Quotation Requests -->
                @if($lead->quotationRequests && $lead->quotationRequests->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quotation Requests</h3>
                    <div class="space-y-3">
                        @foreach($lead->quotationRequests as $quotationRequest)
                            <div class="border border-gray-200 rounded-lg p-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $quotationRequest->product->name }}</p>
                                        <p class="text-xs text-gray-500">Qty: {{ $quotationRequest->quantity }} {{ $quotationRequest->size ? '• Size: ' . $quotationRequest->size : '' }}</p>
                                        <p class="text-xs text-gray-400">{{ formatDubaiDate($quotationRequest->created_at, 'M j, Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $quotationRequest->status_badge_class }}">
                                            {{ $quotationRequest->formatted_status }}
                                    </span>
                                        @if($quotationRequest->supplier_response === 'available')
                                            <p class="text-xs text-green-600 mt-1">✓ Supplier responded</p>
                                        @elseif($quotationRequest->supplier_response === 'not_available')
                                            <p class="text-xs text-red-600 mt-1">✗ Not available</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-3 flex space-x-2">
                                    <a href="{{ route('admin.inquiries.show', $quotationRequest) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800">View Details</a>
                                    
                                    @if($quotationRequest->status === 'supplier_responded' && $quotationRequest->supplier_response === 'available')
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('admin.inquiries.show', $quotationRequest) }}#generate-quote" 
                                           class="text-xs text-green-600 hover:text-green-800">Generate Customer Quote</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function toggleActivityForm() {
    const form = document.getElementById('activityForm');
    form.classList.toggle('hidden');
}

function toggleDealForm() {
    const form = document.getElementById('dealForm');
    form.classList.toggle('hidden');
}

function toggleQuotationForm() {
    const form = document.getElementById('quotationForm');
    form.classList.toggle('hidden');
}

document.getElementById('status').addEventListener('change', function() {
    const dueDateField = document.getElementById('dueDateField');
    if (this.value === 'scheduled') {
        dueDateField.classList.remove('hidden');
        document.getElementById('due_date').required = true;
    } else {
        dueDateField.classList.add('hidden');
        document.getElementById('due_date').required = false;
    }
});
</script>
@endsection 