@extends('layouts.crm')

@section('title', 'Contact Submission Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('crm.contact-submissions.index') }}" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Contact Submission Details</h1>
                </div>
                <p class="text-gray-600 mt-2">View and manage contact form submission</p>
            </div>
            <div class="flex space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $submission->status_badge_class }}">
                    {{ $submission->formatted_status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Contact Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $submission->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1">
                                <a href="mailto:{{ $submission->email }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ $submission->email }}
                                </a>
                            </div>
                        </div>
                        @if($submission->phone)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <div class="mt-1">
                                <a href="tel:{{ $submission->phone }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ $submission->phone }}
                                </a>
                            </div>
                        </div>
                        @endif
                        @if($submission->company)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Company</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $submission->company }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Message Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Message Details</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subject</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $submission->subject }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Message</label>
                            <div class="mt-1 text-sm text-gray-900 bg-gray-50 p-4 rounded-md">
                                {{ $submission->message }}
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-500">
                            <div>
                                <span class="font-medium">Submitted:</span> {{ $submission->created_at ? $submission->created_at->format('M j, Y g:i A') : 'N/A' }}
                            </div>
                            @if($submission->responded_at)
                            <div>
                                <span class="font-medium">Responded:</span> {{ $submission->responded_at->format('M j, Y g:i A') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lead Potential Assessment -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Lead Potential Assessment</h3>
                </div>
                <div class="p-6">
                    @if($submission->isSalesInquiry())
                        <div class="flex items-center space-x-3 p-4 bg-green-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-star text-green-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-green-900">High Sales Potential</h4>
                                <p class="text-sm text-green-700">This appears to be a sales inquiry with conversion potential.</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-3 p-4 bg-gray-50 rounded-lg">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-gray-600 text-lg"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">General Inquiry</h4>
                                <p class="text-sm text-gray-700">This appears to be a general inquiry or information request.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Admin Notes -->
            @if($submission->admin_notes)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Admin Notes</h3>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ $submission->admin_notes }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Email Contact -->
                    <a href="mailto:{{ $submission->email }}" 
                       class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-center font-medium flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i>Send Email
                    </a>

                    @if($submission->phone)
                    <!-- Call Contact -->
                    <a href="tel:{{ $submission->phone }}" 
                       class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-center font-medium flex items-center justify-center">
                        <i class="fas fa-phone mr-2"></i>Call Now
                    </a>
                    @endif
                </div>
            </div>

            <!-- Status Update -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Update Status</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('crm.contact-submissions.status.update', ['submission' => $submission->id]) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="new" {{ $submission->status == 'new' ? 'selected' : '' }}>New</option>
                                <option value="in_review" {{ $submission->status == 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="responded" {{ $submission->status == 'responded' ? 'selected' : '' }}>Responded</option>
                                <option value="closed" {{ $submission->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Add Notes</label>
                            <textarea name="admin_notes" rows="3" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Add your notes..."></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Conversion Actions -->
            @if($submission->canConvertToLead() || $submission->canConvertToInquiry())
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Convert Submission</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($submission->canConvertToLead())
                    <!-- Convert to Lead Button -->
                    <button type="button" onclick="openConvertToLeadModal()" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2"></i>Convert to Lead
                    </button>
                    @endif

                    @if($submission->canConvertToInquiry())
                    <!-- Convert to Inquiry Button -->
                    <button type="button" onclick="openConvertToInquiryModal()" 
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-medium flex items-center justify-center">
                        <i class="fas fa-shopping-cart mr-2"></i>Convert to Inquiry
                    </button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Assignment Info -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Assignment</h3>
                </div>
                <div class="p-6">
                    @if($submission->assignedTo)
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($submission->assignedTo->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $submission->assignedTo->name }}</div>
                                <div class="text-xs text-gray-500">Assigned</div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-gray-500">Not assigned</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convert to Lead Modal -->
@if($submission->canConvertToLead())
<div id="convertToLeadModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Convert to CRM Lead</h3>
                <button type="button" onclick="closeConvertToLeadModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('crm.contact-submissions.convert-to-lead', ['submission' => $submission->id]) }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lead Source</label>
                            <select name="lead_source" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Source</option>
                                <option value="website">Website Contact Form</option>
                                <option value="referral">Referral</option>
                                <option value="linkedin">LinkedIn</option>
                                <option value="email">Email Marketing</option>
                                <option value="google_ads">Google Ads</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lead Status</label>
                        <select name="lead_status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="new">New</option>
                            <option value="contacted">Contacted</option>
                            <option value="qualified">Qualified</option>
                            <option value="proposal">Proposal</option>
                            <option value="negotiation">Negotiation</option>
                            <option value="won">Won</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Value (AED)</label>
                        <input type="number" name="estimated_value" min="0" step="0.01" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="0.00">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CRM Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Add any additional notes for the CRM lead..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeConvertToLeadModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Convert to Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Convert to Inquiry Modal -->
@if($submission->canConvertToInquiry())
<div id="convertToInquiryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Convert to Quotation Request</h3>
                <button type="button" onclick="closeConvertToInquiryModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('crm.contact-submissions.convert-to-inquiry', ['submission' => $submission->id]) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="product_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" required min="1" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="1">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Size/Specifications</label>
                        <input type="text" name="size" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., 250ml, Standard">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Requirements</label>
                    <textarea name="requirements" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Any special requirements or specifications..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CRM Notes</label>
                    <textarea name="notes" rows="3" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Add any additional notes for the quotation request..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeConvertToInquiryModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-medium">
                        Convert to Inquiry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// Convert to Lead Modal
function openConvertToLeadModal() {
    document.getElementById('convertToLeadModal').classList.remove('hidden');
}

function closeConvertToLeadModal() {
    document.getElementById('convertToLeadModal').classList.add('hidden');
}

// Convert to Inquiry Modal
function openConvertToInquiryModal() {
    document.getElementById('convertToInquiryModal').classList.remove('hidden');
}

function closeConvertToInquiryModal() {
    document.getElementById('convertToInquiryModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    const leadModal = document.getElementById('convertToLeadModal');
    const inquiryModal = document.getElementById('convertToInquiryModal');
    
    if (event.target === leadModal) {
        closeConvertToLeadModal();
    }
    
    if (event.target === inquiryModal) {
        closeConvertToInquiryModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConvertToLeadModal();
        closeConvertToInquiryModal();
    }
});
</script>
@endsection 