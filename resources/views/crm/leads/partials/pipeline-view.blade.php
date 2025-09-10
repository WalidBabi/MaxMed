<!-- Enhanced Sales Pipeline Board with Advanced UX -->
<div class="mb-6">
    <!-- Modern Pipeline Status Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        @if(isset($pipelineData))
            @foreach($pipelineData as $status => $stage)
                <div class="group relative rounded-2xl border-2 transition-all duration-200 cursor-pointer metric-card overflow-hidden
                     @switch($status)
                         @case('new_inquiry')
                             bg-gradient-to-br from-cyan-50 to-cyan-100 border-cyan-200 hover:border-cyan-400 hover:from-cyan-100 hover:to-cyan-150
                             @break
                         @case('quote_requested')
                             bg-gradient-to-br from-gray-50 to-gray-100 border-gray-200 hover:border-gray-400 hover:from-gray-100 hover:to-gray-150
                             @break
                         @case('follow_up_1')
                             bg-gradient-to-br from-sky-50 to-sky-100 border-sky-200 hover:border-sky-400 hover:from-sky-100 hover:to-sky-150
                             @break
                         @case('follow_up_2')
                             bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200 hover:border-blue-400 hover:from-blue-100 hover:to-blue-150
                             @break
                         @case('follow_up_3')
                             bg-gradient-to-br from-indigo-50 to-indigo-100 border-indigo-200 hover:border-indigo-400 hover:from-indigo-100 hover:to-indigo-150
                             @break
                         @case('quote_sent')
                             bg-gradient-to-br from-teal-50 to-teal-100 border-teal-200 hover:border-teal-400 hover:from-teal-100 hover:to-teal-150
                             @break
                         @case('negotiating_price')
                             bg-gradient-to-br from-orange-50 to-orange-100 border-orange-200 hover:border-orange-400 hover:from-orange-100 hover:to-orange-150
                             @break
                         @case('payment_pending')
                             bg-gradient-to-br from-green-50 to-green-100 border-green-200 hover:border-green-400 hover:from-green-100 hover:to-green-150
                             @break
                         @case('order_confirmed')
                             bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200 hover:border-emerald-400 hover:from-emerald-100 hover:to-emerald-150
                             @break
                         @case('deal_lost')
                             bg-gradient-to-br from-slate-50 to-slate-100 border-slate-200 hover:border-slate-400 hover:from-slate-100 hover:to-slate-150
                             @break
                     @endswitch" 
                     data-stage="{{ $status }}">
                    
                    <!-- Top Status Bar -->
                    <div class="h-2 
                        @switch($status)
                            @case('new_inquiry')
                                bg-gradient-to-r from-cyan-400 to-cyan-600
                                @break
                            @case('quote_requested')
                                bg-gradient-to-r from-gray-400 to-gray-600
                                @break
                            @case('follow_up_1')
                                bg-gradient-to-r from-sky-400 to-sky-600
                                @break
                            @case('follow_up_2')
                                bg-gradient-to-r from-blue-400 to-blue-600
                                @break
                            @case('follow_up_3')
                                bg-gradient-to-r from-indigo-400 to-indigo-600
                                @break
                            @case('quote_sent')
                                bg-gradient-to-r from-teal-400 to-teal-600
                                @break
                            @case('negotiating_price')
                                bg-gradient-to-r from-orange-400 to-orange-600
                                @break
                            @case('payment_pending')
                                bg-gradient-to-r from-green-400 to-green-600
                                @break
                            @case('order_confirmed')
                                bg-gradient-to-r from-emerald-400 to-emerald-600
                                @break
                            @case('deal_lost')
                                bg-gradient-to-r from-slate-400 to-slate-600
                                @break
                        @endswitch"></div>
                    
                    <!-- Card Content -->
                    <div class="p-4">
                        <!-- Status Icon & Title -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    @switch($status)
                                        @case('new_inquiry')
                                            bg-cyan-100
                                            @break
                                        @case('quote_requested')
                                            bg-gray-100
                                            @break
                                        @case('follow_up_1')
                                            bg-sky-100
                                            @break
                                        @case('follow_up_2')
                                            bg-blue-100
                                            @break
                                        @case('follow_up_3')
                                            bg-indigo-100
                                            @break
                                        @case('quote_sent')
                                            bg-teal-100
                                            @break
                                        @case('negotiating_price')
                                            bg-orange-100
                                            @break
                                        @case('payment_pending')
                                            bg-green-100
                                            @break
                                        @case('order_confirmed')
                                            bg-emerald-100
                                            @break
                                        @case('deal_lost')
                                            bg-slate-100
                                            @break
                                    @endswitch">
                                                                        @switch($status)
                                        @case('new_inquiry')
                                            <svg class="w-4 h-4 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                            @break
                                        @case('quote_requested')
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            @break
                                        @case('follow_up_1')
                                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            @break
                                        @case('follow_up_2')
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                            @break
                                        @case('follow_up_3')
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                            @break
                                        @case('quote_sent')
                                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                            @break
                                        @case('negotiating_price')
                                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                            @break
                                        @case('payment_pending')
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            @break
                                        @case('order_confirmed')
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                            @break
                                        @case('deal_lost')
                                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                            @break
                                        @default
                                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-600 truncate">
                                        @switch($status)
                                            @case('new_inquiry')
                                                New Inquiry
                                                @break
                                            @case('quote_requested')
                                                Quote Request
                                                @break
                                            @case('follow_up_1')
                                                Follow-up 1
                                                @break
                                            @case('follow_up_2')
                                                Follow-up 2
                                                @break
                                            @case('follow_up_3')
                                                Follow-up 3
                                                @break
                                            @case('quote_sent')
                                                Quote Sent
                                                @break
                                            @case('negotiating_price')
                                                Negotiating
                                                @break
                                            @case('payment_pending')
                                                Payment Due
                                                @break
                                            @case('order_confirmed')
                                                Confirmed
                                                @break
                                            @case('deal_lost')
                                                Lost
                                                @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                        @endswitch
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Count & Value -->
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-2xl font-bold leading-none
                                    @switch($status)
                                        @case('new_inquiry')
                                            text-cyan-700
                                            @break
                                        @case('quote_requested')
                                            text-gray-700
                                            @break
                                        @case('follow_up_1')
                                            text-sky-700
                                            @break
                                        @case('follow_up_2')
                                            text-blue-700
                                            @break
                                        @case('follow_up_3')
                                            text-indigo-700
                                            @break
                                        @case('quote_sent')
                                            text-teal-700
                                            @break
                                        @case('negotiating_price')
                                            text-orange-700
                                            @break
                                        @case('payment_pending')
                                            text-green-700
                                            @break
                                        @case('order_confirmed')
                                            text-emerald-700
                                            @break
                                        @case('deal_lost')
                                            text-slate-700
                                            @break
                                    @endswitch">{{ $stage['count'] }}</p>
                                @if($stage['total_value'] > 0)
                                    <p class="text-xs text-gray-500 font-medium mt-1">AED {{ number_format($stage['total_value'], 0) }}</p>
                                @endif
                            </div>
                            
                            <!-- Status Indicators -->
                            <div class="flex flex-col items-end space-y-1">
                                @if($stage['high_priority_count'] > 0)
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                        <span class="text-xs font-medium text-red-600">{{ $stage['high_priority_count'] }}</span>
                                    </div>
                                @endif
                                @if($stage['overdue_count'] > 0)
                                    <div class="flex items-center space-x-1">
                                        <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                        <span class="text-xs font-medium text-orange-600">{{ $stage['overdue_count'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    

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
                                <!-- Compact Status Card -->
                                <div class="group relative bg-white rounded-lg shadow-sm border-l-4 border-{{ $stage['color'] }}-500 hover:shadow-md transition-all duration-200 cursor-pointer lead-card" 
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

                                    <!-- Card Content -->
                                    <div class="p-3">
                                        <!-- Header Row -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2 min-w-0 flex-1">
                                                <!-- Status Indicator -->
                                                <div class="w-2 h-2 rounded-full bg-{{ $stage['color'] }}-500 flex-shrink-0"></div>
                                                
                                                <!-- Name & Company -->
                                                    <div class="min-w-0 flex-1">
                                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $lead->full_name }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ $lead->company_name }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Priority Indicator -->
                                                @if($lead->priority === 'high')
                                                <div class="w-3 h-3 rounded-full bg-red-500 flex-shrink-0" title="High Priority"></div>
                                                @elseif($lead->priority === 'medium')
                                                <div class="w-3 h-3 rounded-full bg-yellow-500 flex-shrink-0" title="Medium Priority"></div>
                                                @else
                                                <div class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0" title="Low Priority"></div>
                                                @endif
                                        </div>

                                        <!-- Status Badge -->
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $stage['color'] }}-100 text-{{ $stage['color'] }}-800 border border-{{ $stage['color'] }}-200">
                                                @switch($status)
                                                    @case('new_inquiry')
                                                        📩 New Inquiry
                                                        @break
                                                    @case('quote_requested')
                                                        💰 Quote Requested
                                                        @break
                                                    @case('follow_up_1')
                                                        ⏰ Follow-up 1
                                                        @break
                                                    @case('follow_up_2')
                                                        🔔 Follow-up 2
                                                        @break
                                                    @case('follow_up_3')
                                                        🚨 Follow-up 3
                                                        @break
                                                    @case('quote_sent')
                                                        📤 Quote Sent
                                                        @break
                                                    @case('negotiating_price')
                                                        🤝 Price Negotiation
                                                        @break
                                                    @case('payment_pending')
                                                        💳 Payment Pending
                                                        @break
                                                    @case('order_confirmed')
                                                        ✅ Confirmed
                                                        @break
                                                    @case('deal_lost')
                                                        ❌ Lost
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                @endswitch
                                            </span>
                                            
                                            <!-- Value -->
                                            @if($lead->estimated_value)
                                                <span class="text-xs font-semibold text-green-600">AED {{ number_format($lead->estimated_value, 0) }}</span>
                                            @endif
                                        </div>

                                        <!-- Contact & Source Row -->
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center space-x-1">
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
                                                
                                                <!-- Assigned User -->
                                                @if($lead->assignedUser)
                                                    <div class="flex items-center space-x-1 ml-2">
                                                        <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        <span class="text-blue-600 font-medium truncate max-w-20" title="{{ $lead->assignedUser->name }}">
                                                            {{ $lead->assignedUser->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Age Since Creation (days hours minutes) -->
                                            <span class="text-gray-400 font-medium" title="{{ $lead->created_at->format('Y-m-d H:i') }}">
                                                {{ $lead->created_ago }}
                                            </span>
                                        </div>

                                        <!-- Source Row -->
                                        <div class="flex items-center justify-end text-xs text-gray-500">
                                            <!-- Source -->
                                            <span class="capitalize text-{{ $stage['color'] }}-600 font-medium">
                                                @switch($lead->source)
                                                    @case('whatsapp')
                                                        📱 WhatsApp
                                                        @break
                                                    @case('email')
                                                        📧 Email
                                                        @break
                                                    @case('website')
                                                        🌐 Web
                                                        @break
                                                    @case('phone')
                                                        ☎️ Phone
                                                        @break
                                                    @case('linkedin')
                                                        💼 LinkedIn
                                                        @break
                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $lead->source)) }}
                                                @endswitch
                                            </span>
                                        </div>

                                        <!-- Overdue Indicator -->
                                        @if($lead->isOverdue())
                                            <div class="mt-2 flex items-center text-xs text-red-600">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Overdue ({{ (int) floor($lead->daysSinceLastContact()) }} days)
                                            </div>
                                        @endif

                                        <!-- Quick Action Buttons (Hidden by default, shown on hover) -->
                                        <div class="mt-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <button onclick="event.stopPropagation(); window.location.href='/crm/leads/{{ $lead->id }}'" 
                                                    class="flex-1 text-xs py-1 px-2 bg-{{ $stage['color'] }}-100 text-{{ $stage['color'] }}-700 rounded hover:bg-{{ $stage['color'] }}-200 transition-colors"
                                                        title="View Details">
                                                View
                                                </button>
                                                <button onclick="event.stopPropagation(); window.location.href='/crm/leads/{{ $lead->id }}/edit'" 
                                                    class="flex-1 text-xs py-1 px-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                                                        title="Edit Lead">
                                                Edit
                                                </button>
                                        </div>
                                    </div>

                                    <!-- Hover Effect -->
                                    <div class="absolute inset-0 bg-{{ $stage['color'] }}-50 opacity-0 group-hover:opacity-20 transition-opacity duration-200 rounded-lg pointer-events-none"></div>
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