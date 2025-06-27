<!-- Table View -->
<div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Statistics Cards -->
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <!-- Total Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $counts['all'] }}</p>
                </div>
                <div class="bg-gray-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending</p>
                    <p class="mt-1 text-2xl font-semibold text-orange-600">{{ $counts['pending'] }}</p>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Viewed Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Viewed</p>
                    <p class="mt-1 text-2xl font-semibold text-blue-600">{{ $counts['viewed'] }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Quoted Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Quoted</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600">{{ $counts['quoted'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Accepted Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Accepted</p>
                    <p class="mt-1 text-2xl font-semibold text-indigo-600">{{ $counts['accepted'] }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Not Available Card -->
        <div class="bg-white rounded-lg shadow p-4 border border-gray-200 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Not Available</p>
                    <p class="mt-1 text-2xl font-semibold text-red-600">{{ $counts['not_available'] }}</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Reference
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Quantity
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($allInquiries as $inquiry)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ substr($inquiry->reference_number ?? 'N/A', 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $inquiry->reference_number ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($inquiry->product && $inquiry->product->primaryImage)
                                    <img src="{{ asset('storage/' . $inquiry->product->primaryImage->image_path) }}" 
                                         alt="{{ $inquiry->product->name }}" 
                                         class="h-10 w-10 rounded-lg object-cover mr-3">
                                @else
                                    <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $inquiry->product->name ?? $inquiry->product_name ?? 'N/A' }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ Str::limit($inquiry->requirements ?? '', 50) }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $response = $inquiry->supplierResponses->where('user_id', auth()->id())->first();
                                $quotation = $inquiry->quotations->where('supplier_id', auth()->id())->first();
                                
                                // Determine the actual status by checking both response and quotation
                                $actualStatus = 'pending';
                                if ($quotation) {
                                    if ($quotation->status === 'approved') {
                                        $actualStatus = 'accepted';
                                    } else {
                                        $actualStatus = 'quoted';
                                    }
                                } elseif ($response) {
                                    $actualStatus = $response->status;
                                }

                                $statusClass = match($actualStatus) {
                                    'pending' => 'bg-orange-100 text-orange-800',
                                    'viewed' => 'bg-blue-100 text-blue-800',
                                    'quoted' => 'bg-green-100 text-green-800',
                                    'accepted' => 'bg-indigo-100 text-indigo-800',
                                    'not_available' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                
                                $statusText = match($actualStatus) {
                                    'pending' => 'Pending',
                                    'viewed' => 'Viewed',
                                    'quoted' => 'Quoted',
                                    'accepted' => 'Accepted',
                                    'not_available' => 'Not Available',
                                    default => ucfirst($actualStatus)
                                };
                            @endphp
                            <span class="inline-flex flex-col rounded-lg px-2 py-1 text-xs {{ $statusClass }}">
                                <span class="font-semibold">{{ $statusText }}</span>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($inquiry->quantity ?? 0) }}
                            <span class="text-gray-500 text-xs">units</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div>{{ $inquiry->created_at->format('M d, Y') }}</div>
                            <div class="text-xs">{{ $inquiry->created_at->format('h:i A') }}</div>
                            @if($inquiry->expires_at && $inquiry->expires_at->isFuture())
                                <div class="mt-1">
                                    <span class="text-xs inline-flex items-center px-2 py-0.5 rounded-full {{ $inquiry->expires_at->diffInDays() <= 1 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        Expires {{ $inquiry->expires_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-3 justify-end">
                                <a href="{{ route('supplier.inquiries.show', $inquiry->id) }}" 
                                   class="text-indigo-600 hover:text-indigo-900">
                                    View
                                </a>
                                @if(in_array($actualStatus, ['pending', 'viewed']))
                                    <button type="button" 
                                            class="text-green-600 hover:text-green-900 quotation-btn"
                                            data-inquiry-id="{{ $inquiry->id }}">
                                        Quote
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.004 0 7.625 2.371 9.287 6M32 14a4 4 0 11-8 0 4 4 0 018 0zm-8 6a9 9 0 019 9v1H15v-1a9 9 0 019-9z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No inquiries found</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    @if(request()->filled('search'))
                                        No inquiries match your search criteria.
                                    @elseif(request()->filled('status'))
                                        No inquiries with status "{{ request('status') }}".
                                    @elseif(request()->filled('date_range'))
                                        No inquiries in the selected date range.
                                    @elseif(request()->filled('category'))
                                        No inquiries in the selected category.
                                    @else
                                        No inquiries available.
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle quotation button clicks
    document.querySelectorAll('.quotation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const inquiryId = this.dataset.inquiryId;
            window.location.href = `/supplier/inquiries/${inquiryId}/quotation`;
        });
    });
});
</script>
@endpush 