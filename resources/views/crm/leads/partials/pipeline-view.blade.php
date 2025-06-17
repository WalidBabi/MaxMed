<!-- Sales Pipeline Board -->
<div class="mb-6">
    <!-- Pipeline Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        @if(isset($pipelineData))
            @foreach($pipelineData as $status => $stage)
                <div class="bg-white rounded-lg shadow-sm border border-{{ $stage['color'] }}-200 p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $stage['title'] }}</p>
                            <p class="text-2xl font-bold text-{{ $stage['color'] }}-600">{{ $stage['count'] }}</p>
                        </div>
                        <div class="text-right">
                            @if($stage['total_value'] > 0)
                                <p class="text-xs text-gray-500">AED {{ number_format($stage['total_value'], 0) }}</p>
                            @endif
                            @if($stage['high_priority_count'] > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ $stage['high_priority_count'] }} High Priority
                                </span>
                            @endif
                            @if($stage['overdue_count'] > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 mt-1">
                                    {{ $stage['overdue_count'] }} Overdue
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- Pipeline Columns -->
    <div class="flex overflow-x-auto space-x-6 pb-6 pipeline-scroll-container">
        @if(isset($pipelineData))
            @foreach($pipelineData as $status => $stage)
                <div class="flex-shrink-0 w-80">
                    <!-- Column Header -->
                    <div class="bg-{{ $stage['color'] }}-50 border border-{{ $stage['color'] }}-200 rounded-t-lg p-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-{{ $stage['color'] }}-800">
                                {{ $stage['title'] }}
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $stage['color'] }}-100 text-{{ $stage['color'] }}-800">
                                    {{ $stage['count'] }}
                                </span>
                            </h3>
                            <div class="flex items-center space-x-2">
                                @if($stage['total_value'] > 0)
                                    <span class="text-sm font-medium text-{{ $stage['color'] }}-700">
                                        AED {{ number_format($stage['total_value'], 0) }}
                                    </span>
                                @endif
                                <button class="text-{{ $stage['color'] }}-600 hover:text-{{ $stage['color'] }}-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Lead Cards -->
                    <div class="bg-{{ $stage['color'] }}-25 border-l border-r border-b border-{{ $stage['color'] }}-200 rounded-b-lg min-h-96 p-4 space-y-4 drop-zone" 
                         data-status="{{ $status }}"
                         ondragover="handleDragOver(event)"
                         ondrop="handleDrop(event)"
                         ondragenter="handleDragEnter(event)"
                         ondragleave="handleDragLeave(event)">
                        @forelse($stage['leads'] as $lead)
                            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow duration-200 cursor-pointer lead-card" 
                                 data-lead-id="{{ $lead->id }}"
                                 data-current-status="{{ $status }}"
                                 onclick="openLeadModal({{ $lead->id }}, '{{ $lead->full_name }}')"
                                 draggable="true"
                                 ondragstart="handleDragStart(event)"
                                 ondragend="handleDragEnd(event)">
                                <!-- Lead Card Header -->
                                <div class="p-4 border-b border-gray-100">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-{{ $stage['color'] }}-100 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-{{ $stage['color'] }}-800">
                                                        {{ strtoupper(substr($lead->first_name, 0, 1) . substr($lead->last_name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $lead->full_name }}</p>
                                                <p class="text-sm text-gray-500 truncate">{{ $lead->company_name }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <!-- Priority Badge -->
                                            @if($lead->priority === 'high')
                                                <span class="inline-flex items-center p-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            @elseif($lead->priority === 'medium')
                                                <span class="inline-flex items-center p-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                            
                                            <!-- Overdue Indicator -->
                                            @if($lead->isOverdue())
                                                <span class="inline-flex items-center p-1 rounded-full text-xs font-medium bg-red-100 text-red-800" title="Overdue">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Lead Card Body -->
                                <div class="p-4">
                                    <div class="space-y-3">
                                        <!-- Contact Information -->
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="truncate">{{ $lead->email }}</span>
                                        </div>

                                        @if($lead->phone)
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                <span>{{ $lead->phone }}</span>
                                            </div>
                                        @endif

                                        <!-- Value and Source -->
                                        <div class="flex justify-between items-center">
                                            @if($lead->estimated_value)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    AED {{ number_format($lead->estimated_value, 0) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    No Value
                                                </span>
                                            @endif
                                            
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                                {{ str_replace('_', ' ', $lead->source) }}
                                            </span>
                                        </div>

                                        <!-- Assignment and Last Contact -->
                                        <div class="flex justify-between items-center text-xs text-gray-500">
                                            <span>{{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                                            <span>
                                                @if($lead->last_contacted_at)
                                                    {{ $lead->last_contacted_at->diffForHumans() }}
                                                @else
                                                    Never contacted
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Recent Activity Indicator -->
                                        @if($lead->activities->count() > 0)
                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span>{{ $lead->activities->count() }} activities</span>
                                                @if($lead->activities->first())
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $lead->activities->first()->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quick Action Buttons -->
                                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100">
                                    <div class="flex justify-between">
                                        <div class="flex space-x-2">
                                            <button onclick="event.stopPropagation(); window.location.href='{{ route('crm.leads.show', $lead) }}'" 
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                View
                                            </button>
                                            <button onclick="event.stopPropagation(); window.location.href='{{ route('crm.leads.edit', $lead) }}'" 
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                        </div>
                                        
                                        @if($lead->phone)
                                            <button onclick="event.stopPropagation(); callLead({{ $lead->id }})" 
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                Call
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <p class="text-sm font-medium">No leads in this stage</p>
                                <p class="text-xs text-gray-400 mt-1">Leads will appear here when they reach this stage</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-semibold text-gray-900">No leads found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating your first lead.</p>
                <div class="mt-6">
                    <a href="{{ route('crm.leads.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Lead
                    </a>
                </div>
            </div>
        @endif
    </div>
</div> 