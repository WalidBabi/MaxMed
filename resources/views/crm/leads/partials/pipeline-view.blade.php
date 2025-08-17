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
                    <div class="flex-shrink-0 w-64 pipeline-column" data-stage="{{ $status }}">
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

                        <!-- Compact Lead Cards Container -->
                        <div class="bg-gradient-to-b from-{{ $stage['color'] }}-25 to-{{ $stage['color'] }}-50 border-l border-r border-b border-{{ $stage['color'] }}-200 rounded-b-xl min-h-[400px] p-2 space-y-2 drop-zone transition-all duration-300" 
                             data-status="{{ $status }}"
                             ondragover="handleDragOver(event)"
                             ondrop="handleDrop(event)"
                             ondragenter="handleDragEnter(event)"
                             ondragleave="handleDragLeave(event)">
                            
                            @forelse($stage['leads'] as $lead)
                                <!-- Modern Medical Equipment Lead Card -->
                                <div class="group relative bg-gradient-to-br from-white via-white to-{{ $stage['color'] }}-50 rounded-xl shadow-md border border-{{ $stage['color'] }}-200 hover:shadow-xl hover:border-{{ $stage['color'] }}-300 transition-all duration-300 cursor-pointer lead-card overflow-hidden" 
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
                                    
                                    <!-- Colored Top Border -->
                                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-{{ $stage['color'] }}-400 to-{{ $stage['color'] }}-600"></div>
                                    
                                    <!-- Card Selection Checkbox -->
                                    <div class="absolute top-3 left-3 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200 card-checkbox">
                                        <input type="checkbox" 
                                               class="w-4 h-4 text-{{ $stage['color'] }}-600 bg-white border-2 border-gray-300 rounded focus:ring-{{ $stage['color'] }}-500 focus:ring-2 shadow-sm"
                                               onchange="toggleCardSelection('{{ $lead->id }}', this)"
                                               onclick="event.stopPropagation()">
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-4 pt-5">
                                        <!-- Header with Customer Info -->
                                        <div class="flex items-start justify-between mb-3">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <!-- Medical Icon -->
                                                    <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-{{ $stage['color'] }}-100 to-{{ $stage['color'] }}-200 rounded-lg flex items-center justify-center medical-icon">
                                                        <svg class="w-4 h-4 text-{{ $stage['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-sm font-bold text-gray-900 truncate">{{ $lead->full_name }}</p>
                                                        <p class="text-xs text-gray-600 truncate font-medium">{{ $lead->company_name }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Priority Badge -->
                                            <div class="flex-shrink-0">
                                                @if($lead->priority === 'high')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800 border border-red-200 priority-high">
                                                        🔥 HIGH
                                                    </span>
                                                @elseif($lead->priority === 'medium')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        ⚡ MED
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        ✅ LOW
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Equipment Requirements -->
                                        <div class="mb-3">
                                            <div class="bg-gray-50 rounded-lg p-2 border border-gray-100">
                                                @if($lead->notes)
                                                    <p class="text-xs text-gray-700 line-clamp-2 leading-relaxed">
                                                        <span class="font-medium text-{{ $stage['color'] }}-700">🏥 Requirements:</span>
                                                        {{ $lead->notes }}
                                                    </p>
                                                @else
                                                    <p class="text-xs text-gray-500 italic">
                                                        <span class="font-medium">🏥 Requirements:</span> 
                                                        Awaiting equipment specifications
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Value and Source -->
                                        <div class="flex items-center justify-between mb-3">
                                            @if($lead->estimated_value)
                                                <div class="inline-flex items-center px-2 py-1 bg-green-50 rounded-md border border-green-200">
                                                    <svg class="w-3 h-3 text-green-600 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                    <span class="text-xs font-bold text-green-700">AED {{ number_format($lead->estimated_value, 0) }}</span>
                                                </div>
                                            @else
                                                <div class="inline-flex items-center px-2 py-1 bg-gray-50 rounded-md border border-gray-200">
                                                    <span class="text-xs font-medium text-gray-600">💰 Value TBD</span>
                                                </div>
                                            @endif
                                            
                                            <!-- Source Badge -->
                                            <div class="inline-flex items-center px-2 py-1 bg-{{ $stage['color'] }}-50 rounded-md border border-{{ $stage['color'] }}-200">
                                                @if($lead->source === 'whatsapp')
                                                    <span class="text-xs font-medium text-{{ $stage['color'] }}-700">📱 WhatsApp</span>
                                                @elseif($lead->source === 'email')
                                                    <span class="text-xs font-medium text-{{ $stage['color'] }}-700">📧 Email</span>
                                                @elseif($lead->source === 'website')
                                                    <span class="text-xs font-medium text-{{ $stage['color'] }}-700">🌐 Website</span>
                                                @else
                                                    <span class="text-xs font-medium text-{{ $stage['color'] }}-700 capitalize">{{ str_replace('_', ' ', $lead->source) }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                            <!-- Contact Info -->
                                            <div class="flex items-center space-x-1 text-xs text-gray-500">
                                                @if($lead->phone || $lead->mobile)
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                @endif
                                                @if($lead->email)
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            
                                            <!-- Quick Actions -->
                                            <div class="flex space-x-1">
                                                <button onclick="event.stopPropagation(); window.location.href='/crm/leads/{{ $lead->id }}'" 
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-{{ $stage['color'] }}-100 text-{{ $stage['color'] }}-600 hover:bg-{{ $stage['color'] }}-200 transition-colors duration-200 group"
                                                        title="View Details">
                                                    <svg class="w-3 h-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="event.stopPropagation(); window.location.href='/crm/leads/{{ $lead->id }}/edit'" 
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors duration-200 group"
                                                        title="Edit Lead">
                                                    <svg class="w-3 h-3 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hover Overlay Effect -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-{{ $stage['color'] }}-100/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none rounded-xl"></div>
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