<!-- Traditional Table View -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ strtoupper(substr($lead->first_name, 0, 1) . substr($lead->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $lead->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $lead->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $lead->company_name }}</div>
                            <div class="text-sm text-gray-500">{{ $lead->job_title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->status_color }}-100 text-{{ $lead->status_color }}-800">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $lead->priority_color }}-100 text-{{ $lead->priority_color }}-800">
                                {{ ucfirst($lead->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($lead->estimated_value)
                                AED {{ number_format($lead->estimated_value, 0) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($lead->assignedUser)
                                @if($lead->assignedUser->id === Auth::id())
                                    <span class="font-semibold text-blue-600">{{ $lead->assignedUser->name }}</span>
                                @else
                                    <span>{{ $lead->assignedUser->name }}</span>
                                @endif
                            @else
                                <span class="text-gray-500">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($lead->last_contacted_at)
                                {{ $lead->last_contacted_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                            @if($lead->isOverdue())
                                <span class="ml-1 text-red-600">⚠️</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <button class="send-email-btn text-green-600 hover:text-green-900" 
                                        data-lead-id="{{ $lead->id }}"
                                        data-lead-name="{{ $lead->full_name }}"
                                        data-lead-email="{{ $lead->email }}"
                                        data-assigned-user-email="{{ $lead->assignedUser->email ?? '' }}"
                                        data-assigned-user-name="{{ $lead->assignedUser->name ?? '' }}"
                                        title="Send Email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('crm.leads.show', $lead) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('crm.leads.edit', $lead) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No leads found. <a href="{{ route('crm.leads.create') }}" class="text-blue-600 hover:text-blue-800">Create your first lead</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if(isset($leads) && $leads->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $leads->appends(request()->query())->links() }}
        </div>
    @endif
</div> 