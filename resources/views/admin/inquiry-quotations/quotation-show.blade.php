@extends('admin.layouts.app')

@section('title', 'Quotation Details')

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
                            <dt class="text-sm font-medium text-gray-500">Total Quotation Amount</dt>
                            @php
                                $total = $quotation->items->sum(function($item) {
                                    return ($item->unit_price ?? 0) * ($item->quantity ?? 1);
                                });
                                $currency = $quotation->items->first()->currency ?? 'AED';
                            @endphp
                            <dd class="mt-1 text-sm text-gray-900">{{ number_format($total, 2) }} {{ $currency }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Products</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @foreach($quotation->items as $item)
                                    <div>
                                        {{ $item->product_name ?? ($item->product->name ?? 'Product') }}
                                        @if($item->quantity)
                                            <span class="text-xs text-gray-500">× {{ $item->quantity }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </dd>
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

    <!-- Quoted Products and Uploaded Files -->
    @if($quotation->items && $quotation->items->count() > 0)
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-boxes mr-2"></i>
                    Quoted Products ({{ $quotation->items->count() }})
                </h3>
            </div>
            <div class="p-6 space-y-8">
                @foreach($quotation->items as $item)
                    <div class="border border-gray-100 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start space-x-6">
                            @if($item->product_id && $item->product)
                                @if($item->product->primaryImage)
                                    <img class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-white" src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product->name }}">
                                @elseif($item->product->image_url)
                                    <img class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-white" src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}">
                                @elseif($item->product->images && $item->product->images->count() > 0)
                                    <img class="w-24 h-24 object-contain rounded-lg border border-gray-200 bg-white" src="{{ $item->product->images->first()->image_url }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="w-24 h-24 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400">
                                        <span class="text-sm font-medium text-gray-600">{{ substr($item->product->name, 0, 2) }}</span>
                                    </div>
                                @endif
                            @elseif($item->product_name)
                                <div class="w-24 h-24 flex items-center justify-center rounded-lg border border-gray-200 bg-orange-100 text-orange-600">
                                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @else
                                <div class="w-24 h-24 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-100 text-gray-400">
                                    <svg class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-md font-semibold text-gray-900">{{ $item->product_name ?? ($item->product->name ?? 'Product') }}</span>
                                    @if($item->size)
                                        <span class="text-xs bg-blue-100 text-blue-800 rounded px-2 py-0.5 ml-2">Size: {{ $item->size }}</span>
                                    @endif
                                </div>
                                @if($item->product_description)
                                    <div class="text-xs text-gray-600 mb-1">{{ $item->product_description }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mb-1">
                                    <span>Unit Price:</span> <span class="font-semibold">{{ $item->currency }} {{ number_format($item->unit_price, 2) }}</span>
                                    @if($item->shipping_cost)
                                        <span class="ml-2">+ Shipping: {{ $item->currency }} {{ number_format($item->shipping_cost, 2) }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mb-1">
                                    <span>Quantity:</span> <span class="font-semibold">{{ $item->quantity ?? 1 }}</span>
                                </div>
                                @if($item->notes)
                                    <div class="text-xs text-gray-700 mt-1"><span class="font-semibold">Notes:</span> {{ $item->notes }}</div>
                                @endif
                            </div>
                        </div>
                        @if($item->attachments && is_array($item->attachments) && count($item->attachments) > 0)
                            <div class="mt-4">
                                <div class="font-semibold text-xs text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-paperclip mr-2"></i> Uploaded Files ({{ count($item->attachments) }})
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($item->attachments as $index => $attachment)
                                        @if(isset($attachment['path']))
                                            @php
                                                $fileName = $attachment['name'] ?? 'Document ' . ($index + 1);
                                                $filePath = $attachment['path'];
                                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                $isPdf = $fileExtension === 'pdf';
                                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $fileIcon = 'fas fa-file text-gray-500';
                                                if ($fileExtension === 'pdf') {
                                                    $fileIcon = 'fas fa-file-pdf text-red-500';
                                                } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    $fileIcon = 'fas fa-file-image text-purple-500';
                                                }
                                            @endphp
                                            <div class="border border-gray-200 rounded-lg p-3 bg-white">
                                                <div class="flex items-center mb-2">
                                                    <i class="{{ $fileIcon }} mr-2"></i>
                                                    <span class="text-xs font-medium text-gray-900 truncate" title="{{ $fileName }}">{{ $fileName }}</span>
                                                    <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="ml-auto text-xs text-blue-600 hover:underline">Open</a>
                                                    <a href="{{ asset('storage/' . $filePath) }}" download="{{ $fileName }}" class="ml-2 text-xs text-gray-500 hover:underline">Download</a>
                                                </div>
                                                @if($isPdf)
                                                    <iframe src="{{ asset('storage/' . $filePath) }}" width="100%" height="200px" class="border-0 rounded"></iframe>
                                                @elseif($isImage)
                                                    <img src="{{ asset('storage/' . $filePath) }}" alt="{{ $fileName }}" class="w-full h-32 object-contain rounded border">
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quotation Attachments -->
    @if($quotation->attachments && is_array($quotation->attachments) && count($quotation->attachments) > 0)
        <div class="mt-8 bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-paperclip mr-2"></i>
                    Quotation Attachments ({{ count($quotation->attachments) }})
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($quotation->attachments as $index => $attachment)
                        @if(isset($attachment['path']))
                            @php
                                $fileName = $attachment['name'] ?? 'Document ' . ($index + 1);
                                $filePath = $attachment['path'];
                                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $isPdf = $fileExtension === 'pdf';
                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                $fileIcon = 'fas fa-file text-gray-500';
                                if ($fileExtension === 'pdf') {
                                    $fileIcon = 'fas fa-file-pdf text-red-500';
                                } elseif (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    $fileIcon = 'fas fa-file-image text-purple-500';
                                } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                    $fileIcon = 'fas fa-file-word text-blue-500';
                                } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                    $fileIcon = 'fas fa-file-excel text-green-500';
                                }
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 bg-white">
                                <div class="flex items-center mb-3">
                                    <i class="{{ $fileIcon }} mr-3 text-lg"></i>
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-900 block" title="{{ $fileName }}">{{ $fileName }}</span>
                                        @if(isset($attachment['size']))
                                            <span class="text-xs text-gray-500">{{ round($attachment['size'] / 1024, 2) }} KB</span>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank" 
                                           class="text-sm text-blue-600 hover:underline flex items-center">
                                            <i class="fas fa-external-link-alt mr-1"></i> Open
                                        </a>
                                        <a href="{{ asset('storage/' . $filePath) }}" download="{{ $fileName }}" 
                                           class="text-sm text-gray-500 hover:underline flex items-center">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                                @if($isPdf)
                                    <div class="mt-3">
                                        <iframe src="{{ asset('storage/' . $filePath) }}" width="100%" height="300px" 
                                                class="border-0 rounded border border-gray-200"></iframe>
                                    </div>
                                @elseif($isImage)
                                    <div class="mt-3">
                                        <img src="{{ asset('storage/' . $filePath) }}" alt="{{ $fileName }}" 
                                             class="w-full h-48 object-contain rounded border border-gray-200 bg-gray-50">
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

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