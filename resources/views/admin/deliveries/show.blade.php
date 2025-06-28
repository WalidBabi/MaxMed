@extends('admin.layouts.app')

@section('title', 'Delivery Details')

@section('content')
<div class="-mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Delivery #{{ $delivery->id }}</h1>
                <p class="text-gray-600 mt-2">View delivery details and manage status</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.deliveries.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Back to Deliveries
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Delivery Details -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Delivery Details
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Status</span>
                                <div class="mt-1">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ 
                                        $delivery->status === 'delivered' ? 'bg-green-100 text-green-800' : 
                                        ($delivery->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : 
                                        ($delivery->status === 'processing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500">Order</span>
                                <div class="mt-1">
                                    @if($delivery->order)
                                        <a href="{{ route('admin.orders.show', $delivery->order) }}" class="text-indigo-600 hover:text-indigo-500 font-medium">
                                            {{ $delivery->order->order_number }}
                                        </a>
                                    @else
                                        <span class="text-red-600 font-medium">
                                            Order #{{ $delivery->order_id }} (Order not found)
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($delivery->tracking_number)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Tracking Number</span>
                                    <div class="mt-1 text-gray-900 font-mono">{{ $delivery->tracking_number }}</div>
                                </div>
                            @endif

                            @if($delivery->carrier)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Carrier</span>
                                    <div class="mt-1 text-gray-900">{{ $delivery->carrier }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Shipping Cost</span>
                                <div class="mt-1 text-gray-900 font-semibold">AED {{ number_format($delivery->shipping_cost, 2) }}</div>
                            </div>

                            @if($delivery->total_weight)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Total Weight</span>
                                    <div class="mt-1 text-gray-900">{{ $delivery->total_weight }} kg</div>
                                </div>
                            @endif

                            @if($delivery->shipped_at)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Shipped At</span>
                                    <div class="mt-1 text-gray-900">{{ formatDubaiDate($delivery->shipped_at, 'M d, Y H:i') }}</div>
                                </div>
                            @endif

                            @if($delivery->delivered_at)
                                <div>
                                    <span class="text-sm font-medium text-gray-500">Delivered At</span>
                                    <div class="mt-1 text-gray-900">{{ formatDubaiDate($delivery->delivered_at, 'M d, Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($delivery->notes)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-500">Notes</span>
                            <div class="mt-2 text-gray-900">{{ $delivery->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Shipping Address
                    </h3>
                </div>
                <div class="p-6">
                    <div class="text-gray-900 whitespace-pre-line">{{ $delivery->shipping_address }}</div>
                </div>
            </div>

            <!-- Customer Signature -->
            @if($delivery->customer_signature)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Customer Signature
                        </h3>
                    </div>
                    <div class="p-6 text-center">
                        @if($delivery->customer_signature_url)
                            <img src="{{ $delivery->customer_signature_url }}" alt="Customer Signature" class="mx-auto mb-4 max-h-32 border border-gray-200 rounded">
                            <div class="border-t border-gray-300 w-64 mx-auto"></div>
                            <p class="text-sm text-gray-500 mt-2">Customer's Signature</p>
                        @else
                            <p class="text-gray-500">No signature available</p>
                        @endif
                    </div>
                </div>

                @if($delivery->delivery_conditions)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Delivery Conditions</h3>
                        </div>
                        <div class="p-6">
                            <ul class="space-y-3">
                                @foreach($delivery->delivery_conditions as $condition)
                                    @php
                                        $conditionText = [
                                            'received_undamaged' => 'I confirm that I have received the items in good condition',
                                            'agree_terms' => 'I agree to the terms and conditions of delivery',
                                            'no_damage' => 'I confirm there is no visible damage to the packaging or contents'
                                        ][$condition] ?? $condition;
                                    @endphp
                                    <li class="flex items-center">
                                        <svg class="h-5 w-5 text-green-500 mr-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $conditionText }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @elseif($delivery->isInTransit() && !$delivery->customer_signature)
                <div class="bg-white rounded-lg shadow-sm border border-yellow-200">
                    <div class="px-6 py-4 border-b border-yellow-200 bg-yellow-50">
                        <h3 class="text-lg font-semibold text-yellow-800">Signature Pending</h3>
                    </div>
                    <div class="p-6 text-center">
                        <p class="text-gray-600 mb-4">Customer has not yet signed for this delivery.</p>
                        <a href="{{ route('deliveries.sign', $delivery) }}" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                            </svg>
                            Capture Signature
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="xl:col-span-1 space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @if(!$delivery->isInTransit() && !$delivery->isDelivered())
                            <form action="{{ route('admin.deliveries.mark-as-shipped', $delivery) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                                    <input type="text" id="tracking_number" name="tracking_number" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <div>
                                    <label for="carrier" class="block text-sm font-medium text-gray-700 mb-2">Carrier</label>
                                    <input type="text" id="carrier" name="carrier" required
                                           class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                </div>
                                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5zM12 18.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5zM15.75 18.75a1.5 1.5 0 01-3 0V8.25a1.5 1.5 0 013 0v10.5z" />
                                    </svg>
                                    Mark as Shipped
                                </button>
                            </form>
                        @endif

                        @if($delivery->isInTransit() && !$delivery->isDelivered())
                            <form action="{{ route('admin.deliveries.mark-as-delivered', $delivery) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Mark as Delivered
                                </button>
                            </form>
                        @endif

                        <div class="space-y-2">
                            @if($delivery->order)
                                <a href="{{ route('admin.orders.show', $delivery->order) }}" class="w-full inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    View Order
                                </a>
                            @else
                                <div class="w-full inline-flex items-center rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 shadow-sm ring-1 ring-inset ring-red-200">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                    Order Not Found
                                </div>
                            @endif
                            <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="w-full inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit Delivery
                            </a>
                            <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this delivery?')" 
                                        class="w-full inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-50">
                                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Delete Delivery
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Update -->
            @if($delivery->status !== 'delivered')
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Status Update</h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.deliveries.update-status', $delivery) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                                <select name="status" id="status" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    @foreach(\App\Models\Delivery::$statuses as $value => $label)
                                        <option value="{{ $value }}" {{ $delivery->status === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Final Invoice Information -->
            @if($delivery->finalInvoice()->exists())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Final Invoice
                        </h3>
                    </div>
                    <div class="p-6">
                        @php $finalInvoice = $delivery->finalInvoice()->first(); @endphp
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Invoice #:</span>
                                <a href="{{ route('admin.invoices.show', $finalInvoice) }}" class="text-indigo-600 hover:text-indigo-500">
                                    {{ $finalInvoice->invoice_number }}
                                </a>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Amount:</span>
                                <span class="text-gray-900">{{ number_format($finalInvoice->total_amount, 2) }} {{ $finalInvoice->currency }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-500">Status:</span>
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ 
                                    $finalInvoice->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                    ($finalInvoice->payment_status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')
                                }}">
                                    {{ $finalInvoice::PAYMENT_STATUS[$finalInvoice->payment_status] ?? ucfirst($finalInvoice->payment_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
