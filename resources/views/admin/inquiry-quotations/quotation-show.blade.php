@extends('admin.layouts.app')

@section('title', 'Quotation Details')

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
                    Quotation Details
                </h1>
            </div>
            <p class="mt-2 text-sm text-gray-700">
                Detailed view of quotation from {{ $quotation->supplier->name }}
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
            @if($quotation->status === 'submitted')
                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="document.getElementById('approve-modal').classList.remove('hidden')"
                            class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                        <i class="fas fa-check mr-2"></i> Approve
                    </button>

                </div>
            @endif
        </div>
    </div>

    <!-- Quotation Information -->
    <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quotation Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $quotation->reference_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColors = [
                                        'submitted' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-green-100 text-green-800',
                                    ];
                                    $color = $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($quotation->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Submitted At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $quotation->created_at->format('M d, Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($quotation->unit_price, 2) }} {{ $quotation->currency }}</dd>
                        </div>
                        @if($quotation->delivery_time)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Delivery Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $quotation->delivery_time }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
                <div>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $quotation->supplier->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Product</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $quotation->product ? $quotation->product->name : 'N/A' }}
                            </dd>
                        </div>
                        @if($quotation->notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $quotation->notes }}</dd>
                            </div>
                        @endif
                        @if($quotation->admin_notes)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admin Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $quotation->admin_notes }}</dd>
                            </div>
                        @endif

                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Inquiry Information -->
    @if($quotation->supplierInquiry)
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Related Inquiry</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('admin.inquiry-quotations.show', $quotation->supplierInquiry) }}" 
                               class="text-indigo-600 hover:text-indigo-900">
                                {{ $quotation->supplierInquiry->reference_number }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @php
                                $inquiryStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'broadcast' => 'bg-indigo-100 text-indigo-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'quoted' => 'bg-green-100 text-green-800',
                                    'converted' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $inquiryColor = $inquiryStatusColors[$quotation->supplierInquiry->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $inquiryColor }}">
                                {{ ucfirst($quotation->supplierInquiry->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
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
                            <form action="{{ route('admin.inquiry-quotations.quotations.approve', $quotation) }}" method="POST">
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
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                                    <textarea name="notes" id="notes" rows="3" 
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
                            onclick="document.getElementById('approve-modal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection 