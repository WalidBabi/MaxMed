@extends('admin.layouts.app')

@section('title', 'Inquiry Details')

@php use Illuminate\Support\Str; @endphp

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.inquiry-quotations.index') }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-semibold leading-6 text-gray-900">
                    Inquiry: {{ $inquiry->reference_number }}
                </h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">
                Detailed view of inquiry and all related quotations
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'broadcast' => 'bg-indigo-100 text-indigo-800',
                    'in_progress' => 'bg-blue-100 text-blue-800',
                    'quoted' => 'bg-green-100 text-green-800',
                    'converted' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
                $color = $statusColors[$inquiry->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $color }}">
                {{ ucfirst($inquiry->status) }}
            </span>
        </div>
    </div>

    <!-- Inquiry Information -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Inquiry Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->reference_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $inquiry->product ? $inquiry->product->name : $inquiry->product_name }}
                            </dd>
                        </div>
                        @if($inquiry->quantity)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Quantity</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($inquiry->quantity) }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-4">
                        @if($inquiry->description)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $inquiry->description }}</dd>
                            </div>
                        @endif
                        @if($inquiry->requirements)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Requirements</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $inquiry->requirements }}</dd>
                            </div>
                        @endif
                        @if($inquiry->deadline)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Deadline</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->deadline->format('M d, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Attachments -->
    @if($inquiry->attachments && is_array($inquiry->attachments) && count($inquiry->attachments) > 0)
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Submitted PDF Attachments</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($inquiry->attachments as $attachment)
                        @if(isset($attachment['path']) && Str::endsWith(strtolower($attachment['path']), '.pdf'))
                            <div>
                                <div class="mb-2 font-medium text-gray-700">PDF File</div>
                                <iframe src="{{ asset('storage/' . $attachment['path']) }}" width="100%" height="500px" class="border rounded"></iframe>
                              
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Supplier Response Statistics -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Response Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $responseStats['total_sent'] }}</div>
                    <div class="text-sm text-gray-500">Total Sent</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $responseStats['viewed'] }}</div>
                    <div class="text-sm text-gray-500">Viewed</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $responseStats['quoted'] }}</div>
                    <div class="text-sm text-gray-500">Quoted</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $responseStats['not_available'] }}</div>
                    <div class="text-sm text-gray-500">Not Available</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quotations -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                Quotations ({{ $quotations->count() }})
            </h3>
        </div>
        
        @if($quotations->count() > 0)
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Delivery Time
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Submitted
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($quotations as $quotation)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $quotation->supplier->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $quotation->reference_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ number_format($quotation->unit_price, 2) }} {{ $quotation->currency }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $quotation->delivery_time ?? 'Not specified' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $quotationStatusColors = [
                                            'submitted' => 'bg-yellow-100 text-yellow-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $quotationColor = $quotationStatusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $quotationColor }}">
                                        {{ ucfirst($quotation->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $quotation->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.inquiry-quotations.quotation-show', $quotation) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            View
                                        </a>
                                        @if($quotation->status === 'submitted')
                                            <button type="button" 
                                                    onclick="openApproveModal({{ $quotation->id }})"
                                                    class="inline-flex items-center rounded-md bg-green-600 px-2 py-1 text-xs font-medium text-white hover:bg-green-700">
                                                <i class="fas fa-check mr-1"></i> Approve
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-8 text-center">
                <div class="text-gray-500">
                    <i class="fas fa-file-invoice text-4xl mb-4"></i>
                    <p class="text-lg font-medium">No quotations received yet</p>
                    <p class="text-sm">Quotations from suppliers will appear here once submitted.</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Not Available Responses -->
    @php
        $notAvailableResponses = $inquiry->supplierResponses->where('status', 'not_available');
    @endphp
    @if($notAvailableResponses->count() > 0)
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                    Not Available Responses ({{ $notAvailableResponses->count() }})
                </h3>
            </div>
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reason
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Responded
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notAvailableResponses as $response)
                            <tr class="hover:bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $response->supplier->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $response->supplier->email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-red-700">
                                        @if($response->notes)
                                            {{ $response->notes }}
                                        @else
                                            <span class="text-gray-400">No reason provided</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-red-100 text-red-800">
                                        Not Available
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $response->updated_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Approve Modal -->
<div id="approve-modal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="approveForm" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Approve Quotation
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to approve this quotation? You can add optional notes below.
                                </p>
                                <div class="mt-4">
                                    <label for="approve_notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                    <textarea name="notes" id="approve_notes" rows="3" 
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Approve
                    </button>
                    <button type="button" 
                            onclick="closeApproveModal()"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openApproveModal(quotationId) {
    document.getElementById('approveForm').action = `/admin/inquiry-quotations/quotations/${quotationId}/approve`;
    document.getElementById('approve-modal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approve-modal').classList.add('hidden');
    document.getElementById('approve_notes').value = '';
}
</script>
@endsection 