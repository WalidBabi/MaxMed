@extends('layouts.app')

@section('title', 'Quotation Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Quotation Details</h1>
                <p class="mt-2 text-sm text-gray-700">Review and manage quotation {{ $quotation->quotation_number }}</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <div class="flex space-x-3">
                    @if($quotation->status === 'submitted')
                        <a href="{{ route('admin.quotations.approve', $quotation) }}" 
                           onclick="return confirm('Are you sure you want to approve this quotation?')"
                           class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Approve
                        </a>
                    @endif
                    <a href="{{ route('admin.quotations.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Quotation Information</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Details and status of the quotation.</p>
                </div>
                <div>
                    <span class="inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium
                        @if($quotation->status === 'approved') bg-green-100 text-green-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($quotation->status) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-8 sm:grid-cols-2">
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Quotation Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->quotation_number }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Created Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Supplier</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($quotation->supplier)
                            {{ $quotation->supplier->name }}
                        @else
                            <span class="text-gray-500">Supplier not found</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Product</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        @if($quotation->product)
                            {{ $quotation->product->name }}
                        @elseif($quotation->supplierInquiry && $quotation->supplierInquiry->product)
                            {{ $quotation->supplierInquiry->product->name }}
                            <span class="text-xs text-gray-500">(from inquiry)</span>
                        @elseif($quotation->quotationRequest && $quotation->quotationRequest->product)
                            {{ $quotation->quotationRequest->product->name }}
                            <span class="text-xs text-gray-500">(from request)</span>
                        @else
                            <span class="text-gray-500">Product details not available</span>
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Unit Price</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->currency }} {{ number_format($quotation->unit_price, 2) }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Shipping Cost</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->currency }} {{ number_format($quotation->shipping_cost, 2) }}</dd>
                </div>
                <div class="sm:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">
                        {{ $quotation->currency }} {{ number_format($quotation->unit_price + $quotation->shipping_cost, 2) }}
                        <span class="text-xs text-gray-500">(Unit Price + Shipping)</span>
                    </dd>
                </div>
                @if($quotation->specifications)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Specifications</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->specifications }}</dd>
                </div>
                @endif
                @if($quotation->notes)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Supplier Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->notes }}</dd>
                </div>
                @endif
                @if($quotation->admin_notes)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Admin Notes</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $quotation->admin_notes }}</dd>
                </div>
                @endif

            </dl>
        </div>
    </div>
</div>
@endsection 