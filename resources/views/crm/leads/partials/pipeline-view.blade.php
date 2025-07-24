<!-- Enhanced Sales Pipeline Board with Advanced UX -->
<div class="mb-6">
    <!-- Enhanced Pipeline Statistics with Animations -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        @if(isset($pipelineData))
            @foreach($pipelineData as $status => $stage)
                <div class="bg-gradient-to-br from-white to-{{ $stage['color'] }}-50 rounded-xl shadow-sm border border-{{ $stage['color'] }}-200 p-4 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 metric-card" 
                     data-stage="{{ $status }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-600 uppercase tracking-wide">{{ $stage['title'] }}</p>
                            <p class="text-3xl font-bold text-{{ $stage['color'] }}-600 leading-tight">{{ $stage['count'] }}</p>
                            @if($stage['total_value'] > 0)
                                <p class="text-xs text-gray-500 font-medium">AED {{ number_format($stage['total_value'], 0) }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <div class="w-12 h-12 rounded-full bg-{{ $stage['color'] }}-100 flex items-center justify-center">
                                @if($status === 'new')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                @elseif($status === 'contacted')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                @elseif($status === 'qualified')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @elseif($status === 'proposal')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V9a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($status === 'negotiation')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                @elseif($status === 'won')
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Priority and Overdue Indicators -->
                    @if($stage['high_priority_count'] > 0 || $stage['overdue_count'] > 0)
                        <div class="flex flex-wrap gap-1 mt-3">
                            @if($stage['high_priority_count'] > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $stage['high_priority_count'] }}
                                </span>
                            @endif
                            @if($stage['overdue_count'] > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $stage['overdue_count'] }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>

    <!-- Enhanced Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
            <!-- Search Input -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" 
                           id="pipeline-search" 
                           placeholder="Search leads by name, company, or email..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           onkeyup="filterPipeline()">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="button" 
                            id="clear-search" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 transition-opacity duration-200" 
                            onclick="clearSearch()">
                        <svg class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap items-center space-x-2">
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                        onclick="toggleFilter('priority')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                    </svg>
                    Priority
                </button>
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                        onclick="toggleFilter('source')">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Source
                </button>
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                        onclick="showOverdueOnly()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Overdue
                </button>
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                        onclick="clearAllFilters()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Clear
                </button>
            </div>

            <!-- Bulk Actions -->
            <div class="flex items-center space-x-2">
                <button type="button" 
                        id="bulk-actions-btn"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 opacity-50 cursor-not-allowed"
                        disabled
                        onclick="toggleBulkActions()">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Bulk Actions (<span id="selected-count">0</span>)
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Pipeline Columns with Navigation Arrows -->
    <div class="relative">
        <!-- Navigation Arrows at Top -->
        <div class="absolute top-2 left-1/2 transform -translate-x-1/2 z-30 flex space-x-4">
            <!-- Left Navigation Arrow -->
            <button id="scroll-left-btn" 
                    class="bg-white hover:bg-gray-50 border border-gray-300 rounded-full shadow-lg transition-all duration-200 p-2"
                    onclick="scrollPipelineLeft()"
                    aria-label="Scroll pipeline left">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Right Navigation Arrow -->
            <button id="scroll-right-btn" 
                    class="bg-white hover:bg-gray-50 border border-gray-300 rounded-full shadow-lg transition-all duration-200 p-2"
                    onclick="scrollPipelineRight()"
                    aria-label="Scroll pipeline right">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Pipeline Container -->
        <div class="flex overflow-x-auto space-x-6 pb-6 pipeline-scroll-container scroll-smooth pt-12" id="pipeline-container">
            @if(isset($pipelineData))
                @foreach($pipelineData as $status => $stage)
                    <div class="flex-shrink-0 w-80 pipeline-column" data-stage="{{ $status }}">
                        <!-- Enhanced Column Header -->
                        <div class="bg-gradient-to-r from-{{ $stage['color'] }}-50 to-{{ $stage['color'] }}-100 border border-{{ $stage['color'] }}-200 rounded-t-xl p-4 sticky top-0 z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-bold text-{{ $stage['color'] }}-900">
                                        {{ $stage['title'] }}
                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-{{ $stage['color'] }}-200 text-{{ $stage['color'] }}-800 border border-{{ $stage['color'] }}-300 transform transition-all duration-300 hover:scale-110">
                                        <span class="stage-count" data-stage="{{ $status }}">{{ $stage['count'] }}</span>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($stage['total_value'] > 0)
                                        <span class="text-sm font-bold text-{{ $stage['color'] }}-800 bg-{{ $stage['color'] }}-200 px-2 py-1 rounded-md">
                                            AED {{ number_format($stage['total_value'], 0) }}
                                        </span>
                                    @endif
                                    <div class="relative">
                                        <button class="text-{{ $stage['color'] }}-600 hover:text-{{ $stage['color'] }}-800 p-1 rounded-full hover:bg-{{ $stage['color'] }}-200 transition-all duration-200" 
                                                onclick="toggleColumnMenu('{{ $status }}')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <!-- Column Menu -->
                                        <div id="column-menu-{{ $status }}" 
                                             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-20">
                                            <div class="py-1">
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('{{ $status }}', 'name')">
                                                    Sort by Name
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('{{ $status }}', 'value')">
                                                    Sort by Value
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('{{ $status }}', 'priority')">
                                                    Sort by Priority
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('{{ $status }}', 'date')">
                                                    Sort by Date
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Lead Cards Container -->
                        <div class="bg-gradient-to-b from-{{ $stage['color'] }}-25 to-{{ $stage['color'] }}-50 border-l border-r border-b border-{{ $stage['color'] }}-200 rounded-b-xl min-h-[600px] p-4 space-y-4 drop-zone transition-all duration-300" 
                             data-status="{{ $status }}"
                             ondragover="handleDragOver(event)"
                             ondrop="handleDrop(event)"
                             ondragenter="handleDragEnter(event)"
                             ondragleave="handleDragLeave(event)">
                            
                            @forelse($stage['leads'] as $lead)
                                <!-- Enhanced Lead Card -->
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 cursor-pointer lead-card" 
                                     data-lead-id="{{ $lead->id }}"
                                     data-current-status="{{ $status }}"
                                     data-lead-name="{{ $lead->full_name }}"
                                     data-lead-company="{{ $lead->company_name }}"
                                     data-lead-email="{{ $lead->email }}"
                                     data-lead-priority="{{ $lead->priority }}"
                                     data-lead-source="{{ $lead->source }}"
                                     data-lead-overdue="{{ $lead->isOverdue() ? 'true' : 'false' }}"
                                     data-lead-full-name="{{ $lead->full_name }}"
                                     draggable="true"
                                     ondragstart="handleDragStart(event)"
                                     ondragend="handleDragEnd(event)">
                                    
                                    <!-- Card Selection Checkbox -->
                                    <div class="absolute top-3 left-3 z-10 opacity-0 transition-opacity duration-200 card-checkbox">
                                        <input type="checkbox" 
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                               onchange="toggleCardSelection({{ $lead->id }}, this)"
                                               onclick="event.stopPropagation()">
                                    </div>

                                    <!-- Enhanced Card Header -->
                                    <div class="p-4 border-b border-gray-100">
                                        <div class="flex items-start justify-between">
                                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                                <div class="flex-shrink-0">
                                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-{{ $stage['color'] }}-100 to-{{ $stage['color'] }}-200 flex items-center justify-center shadow-sm border border-{{ $stage['color'] }}-300">
                                                        <span class="text-sm font-bold text-{{ $stage['color'] }}-800">
                                                            {{ strtoupper(substr($lead->first_name, 0, 1) . substr($lead->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-base font-bold text-gray-900 leading-tight">{{ $lead->full_name }}</p>
                                                    <p class="text-sm text-gray-600">{{ $lead->company_name }}</p>
                                                    @if($lead->job_title)
                                                        <p class="text-xs text-gray-500 truncate">{{ $lead->job_title }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end space-y-1 ml-2">
                                                <!-- Priority Badge -->
                                                @if($lead->priority === 'high')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        HIGH
                                                    </span>
                                                @elseif($lead->priority === 'medium')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 2L3 7v11a2 2 0 002 2h10a2 2 0 002-2V7l-7-5z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        MED
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        LOW
                                                    </span>
                                                @endif
                                                
                                                <!-- Overdue Indicator -->
                                                @if($lead->isOverdue())
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200 animate-pulse" title="Overdue - No contact in {{ $lead->daysSinceLastContact() }} days">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $lead->daysSinceLastContact() }}d
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Enhanced Card Body -->
                                    <div class="p-4">
                                        <div class="space-y-3">
                                            <!-- Contact Information -->
                                            <div class="space-y-2">
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <span class="truncate">{{ $lead->email }}</span>
                                                </div>

                                                @if($lead->phone || $lead->mobile)
                                                    <div class="flex items-center text-sm text-gray-600">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                        <span>{{ $lead->phone ?: $lead->mobile }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Value and Source -->
                                            <div class="flex justify-between items-center">
                                                @if($lead->estimated_value)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-800 border border-green-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        AED {{ number_format($lead->estimated_value, 0) }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        TBD
                                                    </span>
                                                @endif
                                                
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 capitalize">
                                                    {{ str_replace('_', ' ', $lead->source) }}
                                                </span>
                                            </div>

                                            <!-- Assignment and Last Contact -->
                                            <div class="flex justify-between items-center text-xs text-gray-500 bg-gray-50 rounded-lg p-2">
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="font-medium">{{ $lead->assignedUser->name ?? 'Unassigned' }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span>
                                                        @if($lead->last_contacted_at)
                                                            {{ $lead->last_contacted_at->diffForHumans() }}
                                                        @else
                                                            Never contacted
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Activity Summary -->
                                            @if($lead->activities->count() > 0)
                                                <div class="flex items-center justify-between text-xs text-gray-500 bg-blue-50 rounded-lg p-2">
                                                    <div class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <span class="font-medium">{{ $lead->activities->count() }} activities</span>
                                                    </div>
                                                    @if($lead->activities->first())
                                                        <span class="text-blue-600">{{ $lead->activities->first()->created_at->format('M d') }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Enhanced Quick Action Buttons -->
                                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-4 py-3 border-t border-gray-200 rounded-b-xl">
                                        <div class="flex justify-between items-center">
                                            <div class="flex space-x-1">
                                                <button onclick="event.stopPropagation(); window.location.href='{{ route('crm.leads.show', $lead) }}'" 
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105"
                                                        title="View Lead Details">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    View
                                                </button>
                                                <button onclick="event.stopPropagation(); window.location.href='{{ route('crm.leads.edit', $lead) }}'" 
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105"
                                                        title="Edit Lead">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                            </div>
                                            
                                            <div class="flex space-x-1">
                                                @if($lead->phone || $lead->mobile)
                                                    <button onclick="event.stopPropagation(); callLead({{ $lead->id }})" 
                                                            class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 transform hover:scale-105"
                                                            title="Call Lead">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                        Call
                                                    </button>
                                                @endif
                                                <button onclick="event.stopPropagation(); emailLead({{ $lead->id }})" 
                                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded-md text-purple-700 bg-purple-100 hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 transform hover:scale-105"
                                                        title="Email Lead">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    Email
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <!-- Enhanced Empty State -->
                                <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">No leads in {{ $stage['title'] }}</p>
                                    <p class="text-xs text-gray-500 text-center max-w-40">Leads will appear here when they reach this stage</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Enhanced No Data State -->
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No leads found</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">Get started by creating your first lead and watch your sales pipeline come to life.</p>
                    <a href="{{ route('crm.leads.create') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent shadow-lg text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200">
                        <svg class="-ml-1 mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Your First Lead
                    </a>
                </div>
            @endif
        </div>
    </div>
</div> 