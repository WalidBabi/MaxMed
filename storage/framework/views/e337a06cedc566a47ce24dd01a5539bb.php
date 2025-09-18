<!-- Enhanced Sales Pipeline Board with Advanced UX -->
<div class="mb-6">
    <!-- Modern Pipeline Status Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-2 mb-6">
        <?php if(isset($pipelineData)): ?>
            <?php $__currentLoopData = $pipelineData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group relative overflow-hidden rounded-md p-1.5 shadow-sm ring-1 transition-all duration-300 hover:shadow-md cursor-pointer metric-card
                     <?php switch($status):
                         case ('new_inquiry'): ?>
                             !bg-gradient-to-br !from-cyan-50 !to-cyan-100 ring-cyan-200/50 hover:ring-cyan-300/50
                             <?php break; ?>
                         <?php case ('quote_requested'): ?>
                             !bg-gradient-to-br !from-slate-50 !to-slate-100 ring-slate-200/50 hover:ring-slate-300/50
                             <?php break; ?>
                         <?php case ('getting_price'): ?>
                             !bg-gradient-to-br !from-indigo-50 !to-indigo-100 ring-indigo-200/50 hover:ring-indigo-300/50
                             <?php break; ?>
                         <?php case ('price_submitted'): ?>
                             !bg-gradient-to-br !from-teal-50 !to-teal-100 ring-teal-200/50 hover:ring-teal-300/50
                             <?php break; ?>
                         <?php case ('follow_up_1'): ?>
                             !bg-gradient-to-br !from-sky-50 !to-sky-100 ring-sky-200/50 hover:ring-sky-300/50
                             <?php break; ?>
                         <?php case ('follow_up_2'): ?>
                             !bg-gradient-to-br !from-blue-50 !to-blue-100 ring-blue-200/50 hover:ring-blue-300/50
                             <?php break; ?>
                         <?php case ('follow_up_3'): ?>
                             !bg-gradient-to-br !from-indigo-50 !to-indigo-100 ring-indigo-200/50 hover:ring-indigo-300/50
                             <?php break; ?>
                         <?php case ('negotiating_price'): ?>
                             !bg-gradient-to-br !from-orange-50 !to-orange-100 ring-orange-200/50 hover:ring-orange-300/50
                             <?php break; ?>
                         <?php case ('payment_pending'): ?>
                             !bg-gradient-to-br !from-green-50 !to-green-100 ring-green-200/50 hover:ring-green-300/50
                             <?php break; ?>
                         <?php case ('order_confirmed'): ?>
                             !bg-gradient-to-br !from-emerald-50 !to-emerald-100 ring-emerald-200/50 hover:ring-emerald-300/50
                             <?php break; ?>
                         <?php case ('deal_lost'): ?>
                             !bg-gradient-to-br !from-slate-50 !to-slate-100 ring-slate-200/50 hover:ring-slate-300/50
                             <?php break; ?>
                     <?php endswitch; ?>"
                     style="background: <?php switch($status):
                         case ('new_inquiry'): ?>
                             linear-gradient(to bottom right, #ecfeff, #cffafe) !important;
                             <?php break; ?>
                         <?php case ('quote_requested'): ?>
                             linear-gradient(to bottom right, #f8fafc, #e2e8f0) !important;
                             <?php break; ?>
                         <?php case ('getting_price'): ?>
                             linear-gradient(to bottom right, #eef2ff, #e0e7ff) !important;
                             <?php break; ?>
                         <?php case ('price_submitted'): ?>
                             linear-gradient(to bottom right, #f0fdfa, #ccfbf1) !important;
                             <?php break; ?>
                         <?php case ('follow_up_1'): ?>
                             linear-gradient(to bottom right, #f0f9ff, #e0f2fe) !important;
                             <?php break; ?>
                         <?php case ('follow_up_2'): ?>
                             linear-gradient(to bottom right, #eff6ff, #dbeafe) !important;
                             <?php break; ?>
                         <?php case ('follow_up_3'): ?>
                             linear-gradient(to bottom right, #eef2ff, #e0e7ff) !important;
                             <?php break; ?>
                         <?php case ('negotiating_price'): ?>
                             linear-gradient(to bottom right, #fff7ed, #fed7aa) !important;
                             <?php break; ?>
                         <?php case ('payment_pending'): ?>
                             linear-gradient(to bottom right, #f0fdf4, #dcfce7) !important;
                             <?php break; ?>
                         <?php case ('order_confirmed'): ?>
                             linear-gradient(to bottom right, #ecfdf5, #d1fae5) !important;
                             <?php break; ?>
                         <?php case ('deal_lost'): ?>
                             linear-gradient(to bottom right, #f8fafc, #e2e8f0) !important;
                             <?php break; ?>
                     <?php endswitch; ?>" 
                     data-stage="<?php echo e($status); ?>">
                    
                    <!-- Decorative Element -->
                    <div class="absolute -right-1 -top-1 h-12 w-12 rounded-full transition-all duration-300 group-hover:scale-110
                        <?php switch($status):
                            case ('new_inquiry'): ?>
                                bg-cyan-200/30
                                <?php break; ?>
                            <?php case ('quote_requested'): ?>
                                bg-slate-200/30
                                <?php break; ?>
                            <?php case ('getting_price'): ?>
                                bg-indigo-200/30
                                <?php break; ?>
                            <?php case ('price_submitted'): ?>
                                bg-teal-200/30
                                <?php break; ?>
                            <?php case ('follow_up_1'): ?>
                                bg-sky-200/30
                                <?php break; ?>
                            <?php case ('follow_up_2'): ?>
                                bg-blue-200/30
                                <?php break; ?>
                            <?php case ('follow_up_3'): ?>
                                bg-indigo-200/30
                                <?php break; ?>
                            <?php case ('negotiating_price'): ?>
                                bg-orange-200/30
                                <?php break; ?>
                            <?php case ('payment_pending'): ?>
                                bg-green-200/30
                                <?php break; ?>
                            <?php case ('order_confirmed'): ?>
                                bg-emerald-200/30
                                <?php break; ?>
                            <?php case ('deal_lost'): ?>
                                bg-slate-200/30
                                <?php break; ?>
                        <?php endswitch; ?>"></div>
                    
                    <!-- Card Content -->
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <div class="flex h-6 w-6 items-center justify-center rounded-md shadow-sm
                                <?php switch($status):
                                    case ('new_inquiry'): ?>
                                        bg-cyan-500
                                        <?php break; ?>
                                    <?php case ('quote_requested'): ?>
                                        bg-slate-500
                                        <?php break; ?>
                                    <?php case ('getting_price'): ?>
                                        bg-indigo-500
                                        <?php break; ?>
                                    <?php case ('price_submitted'): ?>
                                        bg-teal-500
                                        <?php break; ?>
                                    <?php case ('follow_up_1'): ?>
                                        bg-sky-500
                                        <?php break; ?>
                                    <?php case ('follow_up_2'): ?>
                                        bg-blue-500
                                        <?php break; ?>
                                    <?php case ('follow_up_3'): ?>
                                        bg-indigo-500
                                        <?php break; ?>
                                    <?php case ('negotiating_price'): ?>
                                        bg-orange-500
                                        <?php break; ?>
                                    <?php case ('payment_pending'): ?>
                                        bg-green-500
                                        <?php break; ?>
                                    <?php case ('order_confirmed'): ?>
                                        bg-emerald-500
                                        <?php break; ?>
                                    <?php case ('deal_lost'): ?>
                                        bg-slate-500
                                        <?php break; ?>
                                <?php endswitch; ?>">
                                <?php switch($status):
                                    case ('new_inquiry'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('quote_requested'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('getting_price'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('price_submitted'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('follow_up_1'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('follow_up_2'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('follow_up_3'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('negotiating_price'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('payment_pending'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('order_confirmed'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php case ('deal_lost'): ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                <?php endswitch; ?>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-medium uppercase tracking-wide
                                    <?php switch($status):
                                        case ('new_inquiry'): ?>
                                            text-cyan-600
                                            <?php break; ?>
                                        <?php case ('quote_requested'): ?>
                                            text-slate-600
                                            <?php break; ?>
                                        <?php case ('getting_price'): ?>
                                            text-indigo-600
                                            <?php break; ?>
                                        <?php case ('price_submitted'): ?>
                                            text-teal-600
                                            <?php break; ?>
                                        <?php case ('follow_up_1'): ?>
                                            text-sky-600
                                            <?php break; ?>
                                        <?php case ('follow_up_2'): ?>
                                            text-blue-600
                                            <?php break; ?>
                                        <?php case ('follow_up_3'): ?>
                                            text-indigo-600
                                            <?php break; ?>
                                        <?php case ('negotiating_price'): ?>
                                            text-orange-600
                                            <?php break; ?>
                                        <?php case ('payment_pending'): ?>
                                            text-green-600
                                            <?php break; ?>
                                        <?php case ('order_confirmed'): ?>
                                            text-emerald-600
                                            <?php break; ?>
                                        <?php case ('deal_lost'): ?>
                                            text-slate-600
                                            <?php break; ?>
                                    <?php endswitch; ?>">
                                    <?php switch($status):
                                        case ('new_inquiry'): ?>
                                            New Inquiry
                                            <?php break; ?>
                                        <?php case ('quote_requested'): ?>
                                            Quote Request
                                            <?php break; ?>
                                        <?php case ('getting_price'): ?>
                                            Getting Price
                                            <?php break; ?>
                                        <?php case ('price_submitted'): ?>
                                            Price Submitted
                                            <?php break; ?>
                                        <?php case ('follow_up_1'): ?>
                                            Follow-up 1
                                            <?php break; ?>
                                        <?php case ('follow_up_2'): ?>
                                            Follow-up 2
                                            <?php break; ?>
                                        <?php case ('follow_up_3'): ?>
                                            Follow-up 3
                                            <?php break; ?>
                                        <?php case ('negotiating_price'): ?>
                                            Negotiating
                                            <?php break; ?>
                                        <?php case ('payment_pending'): ?>
                                            Payment Due
                                            <?php break; ?>
                                        <?php case ('order_confirmed'): ?>
                                            Confirmed
                                            <?php break; ?>
                                        <?php case ('deal_lost'): ?>
                                            Lost
                                            <?php break; ?>
                                        <?php default: ?>
                                            <?php echo e(ucfirst(str_replace('_', ' ', $status))); ?>

                                    <?php endswitch; ?>
                                </p>
                                <p class="text-xs
                                    <?php switch($status):
                                        case ('new_inquiry'): ?>
                                            text-cyan-500
                                            <?php break; ?>
                                        <?php case ('quote_requested'): ?>
                                            text-slate-500
                                            <?php break; ?>
                                        <?php case ('getting_price'): ?>
                                            text-indigo-500
                                            <?php break; ?>
                                        <?php case ('price_submitted'): ?>
                                            text-teal-500
                                            <?php break; ?>
                                        <?php case ('follow_up_1'): ?>
                                            text-sky-500
                                            <?php break; ?>
                                        <?php case ('follow_up_2'): ?>
                                            text-blue-500
                                            <?php break; ?>
                                        <?php case ('follow_up_3'): ?>
                                            text-indigo-500
                                            <?php break; ?>
                                        <?php case ('negotiating_price'): ?>
                                            text-orange-500
                                            <?php break; ?>
                                        <?php case ('payment_pending'): ?>
                                            text-green-500
                                            <?php break; ?>
                                        <?php case ('order_confirmed'): ?>
                                            text-emerald-500
                                            <?php break; ?>
                                        <?php case ('deal_lost'): ?>
                                            text-slate-500
                                            <?php break; ?>
                                    <?php endswitch; ?>">
                                    <?php if($stage['total_value'] > 0): ?>
                                        AED <?php echo e(number_format($stage['total_value'], 0)); ?>

                                    <?php else: ?>
                                        Lead stage
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="space-y-0">
                                <p class="text-sm font-bold text-gray-900"><?php echo e($stage['count']); ?> Leads</p>
                                <?php if($stage['high_priority_count'] > 0 || $stage['overdue_count'] > 0): ?>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <?php if($stage['high_priority_count'] > 0): ?>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                                <span class="text-xs font-medium text-red-600"><?php echo e($stage['high_priority_count']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($stage['overdue_count'] > 0): ?>
                                            <div class="flex items-center space-x-1">
                                                <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                                                <span class="text-xs font-medium text-orange-600"><?php echo e($stage['overdue_count']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs mt-0.5
                                <?php switch($status):
                                    case ('new_inquiry'): ?>
                                        text-cyan-600
                                        <?php break; ?>
                                    <?php case ('quote_requested'): ?>
                                        text-slate-600
                                        <?php break; ?>
                                    <?php case ('getting_price'): ?>
                                        text-indigo-600
                                        <?php break; ?>
                                    <?php case ('price_submitted'): ?>
                                        text-teal-600
                                        <?php break; ?>
                                    <?php case ('follow_up_1'): ?>
                                        text-sky-600
                                        <?php break; ?>
                                    <?php case ('follow_up_2'): ?>
                                        text-blue-600
                                        <?php break; ?>
                                    <?php case ('follow_up_3'): ?>
                                        text-indigo-600
                                        <?php break; ?>
                                    <?php case ('negotiating_price'): ?>
                                        text-orange-600
                                        <?php break; ?>
                                    <?php case ('payment_pending'): ?>
                                        text-green-600
                                        <?php break; ?>
                                    <?php case ('order_confirmed'): ?>
                                        text-emerald-600
                                        <?php break; ?>
                                    <?php case ('deal_lost'): ?>
                                        text-slate-600
                                        <?php break; ?>
                                <?php endswitch; ?>">
                                <?php if($stage['count'] > 0): ?>
                                    <?php echo e($stage['count']); ?> <?php echo e($stage['count'] == 1 ? 'lead' : 'leads'); ?> in pipeline
                                <?php else: ?>
                                    No leads in this stage
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>

    <!-- Enhanced Search and Filter Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
            <!-- Search Input -->
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" 
                           id="pipeline-search" 
                           placeholder="<?php if(isset($isPurchasingUser) && $isPurchasingUser): ?>Search leads by company name...@elseSearch leads by name, company, or email...<?php endif; ?>" 
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
            <?php if(isset($pipelineData)): ?>
                <?php $__currentLoopData = $pipelineData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex-shrink-0 w-64 pipeline-column" data-stage="<?php echo e($status); ?>">
                        <!-- Enhanced Column Header -->
                        <div class="bg-gradient-to-r from-<?php echo e($stage['color']); ?>-50 to-<?php echo e($stage['color']); ?>-100 border border-<?php echo e($stage['color']); ?>-200 rounded-t-xl p-4 sticky top-0 z-10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-bold text-<?php echo e($stage['color']); ?>-900">
                                        <?php echo e($stage['title']); ?>

                                    </h3>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-<?php echo e($stage['color']); ?>-200 text-<?php echo e($stage['color']); ?>-800 border border-<?php echo e($stage['color']); ?>-300 transform transition-all duration-300 hover:scale-110">
                                        <span class="stage-count" data-stage="<?php echo e($status); ?>"><?php echo e($stage['count']); ?></span>
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if($stage['total_value'] > 0): ?>
                                        <span class="text-sm font-bold text-<?php echo e($stage['color']); ?>-800 bg-<?php echo e($stage['color']); ?>-200 px-2 py-1 rounded-md">
                                            AED <?php echo e(number_format($stage['total_value'], 0)); ?>

                                        </span>
                                    <?php endif; ?>
                                    <div class="relative">
                                        <button class="text-<?php echo e($stage['color']); ?>-600 hover:text-<?php echo e($stage['color']); ?>-800 p-1 rounded-full hover:bg-<?php echo e($stage['color']); ?>-200 transition-all duration-200" 
                                                onclick="toggleColumnMenu('<?php echo e($status); ?>')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                            </svg>
                                        </button>
                                        <!-- Column Menu -->
                                        <div id="column-menu-<?php echo e($status); ?>" 
                                             class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-20">
                                            <div class="py-1">
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('<?php echo e($status); ?>', 'name')">
                                                    Sort by Name
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('<?php echo e($status); ?>', 'value')">
                                                    Sort by Value
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('<?php echo e($status); ?>', 'priority')">
                                                    Sort by Priority
                                                </button>
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" 
                                                        onclick="sortColumn('<?php echo e($status); ?>', 'date')">
                                                    Sort by Date
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Compact Lead Cards Container -->
                        <div class="bg-gradient-to-b from-<?php echo e($stage['color']); ?>-25 to-<?php echo e($stage['color']); ?>-50 border-l border-r border-b border-<?php echo e($stage['color']); ?>-200 rounded-b-xl min-h-[400px] p-2 space-y-2 drop-zone transition-all duration-300" 
                             data-status="<?php echo e($status); ?>"
                             ondragover="handleDragOver(event)"
                             ondrop="handleDrop(event)"
                             ondragenter="handleDragEnter(event)"
                             ondragleave="handleDragLeave(event)">
                            
                            <?php $__empty_1 = true; $__currentLoopData = $stage['leads']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <!-- Compact Status Card -->
                                <div class="group relative bg-white rounded-lg shadow-sm border-l-4 border-<?php echo e($stage['color']); ?>-500 hover:shadow-md transition-all duration-200 cursor-pointer lead-card" 
                                     data-lead-id="<?php echo e($lead->id); ?>"
                                     data-current-status="<?php echo e($status); ?>"
                                     data-lead-name="<?php if(isset($isPurchasingUser) && $isPurchasingUser): ?><?php echo e($lead->notes ? Str::limit($lead->notes, 50) : 'No Requirements'); ?><?php else: ?><?php echo e($lead->full_name); ?><?php endif; ?>"
                                     data-lead-company="<?php if(isset($isPurchasingUser) && $isPurchasingUser): ?>Lead #<?php echo e($lead->id); ?><?php else: ?><?php echo e($lead->company_name); ?><?php endif; ?>"
                                     data-lead-email="<?php echo e($lead->email); ?>"
                                     data-lead-priority="<?php echo e($lead->priority); ?>"
                                     data-lead-source="<?php echo e($lead->source); ?>"
                                     data-lead-overdue="<?php echo e($lead->isOverdue() ? 'true' : 'false'); ?>"
                                     data-lead-full-name="<?php if(isset($isPurchasingUser) && $isPurchasingUser): ?><?php echo e($lead->notes ? Str::limit($lead->notes, 50) : 'No Requirements'); ?><?php else: ?><?php echo e($lead->full_name); ?><?php endif; ?>"
                                     draggable="true"
                                     ondragstart="handleDragStart(event)"
                                     ondragend="handleDragEnd(event)">

                                    <!-- Card Content -->
                                    <div class="p-3">
                                        <!-- Header Row -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-2 min-w-0 flex-1">
                                                <!-- Status Indicator -->
                                                <div class="w-2 h-2 rounded-full bg-<?php echo e($stage['color']); ?>-500 flex-shrink-0"></div>
                                                
                                                <!-- Name & Company -->
                                                    <div class="min-w-0 flex-1">
                                                    <?php if(isset($isPurchasingUser) && $isPurchasingUser): ?>
                                                        <?php if($lead->notes): ?>
                                                            <p class="text-sm font-semibold text-gray-900 truncate" title="<?php echo e($lead->notes); ?>"><?php echo e(Str::limit($lead->notes, 50)); ?></p>
                                                        <?php else: ?>
                                                            <p class="text-sm font-semibold text-gray-900 truncate">No Requirements</p>
                                                        <?php endif; ?>
                                                        <p class="text-xs text-gray-500 truncate">Lead #<?php echo e($lead->id); ?></p>
                                                    <?php else: ?>
                                                        <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($lead->full_name); ?></p>
                                                        <p class="text-xs text-gray-500 truncate"><?php echo e($lead->company_name); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Priority Indicator -->
                                                <?php if($lead->priority === 'high'): ?>
                                                <div class="w-3 h-3 rounded-full bg-red-500 flex-shrink-0" title="High Priority"></div>
                                                <?php elseif($lead->priority === 'medium'): ?>
                                                <div class="w-3 h-3 rounded-full bg-yellow-500 flex-shrink-0" title="Medium Priority"></div>
                                                <?php else: ?>
                                                <div class="w-3 h-3 rounded-full bg-green-500 flex-shrink-0" title="Low Priority"></div>
                                                <?php endif; ?>
                                        </div>

                                        <!-- Value Row -->
                                        <div class="flex items-center justify-between mb-2">
                                            <!-- Value -->
                                            <?php if($lead->estimated_value): ?>
                                                <span class="text-xs font-semibold text-green-600">AED <?php echo e(number_format($lead->estimated_value, 0)); ?></span>
                                            <?php else: ?>
                                                <span></span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Contact & Source Row -->
                                        <div class="flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center space-x-1">
                                                <?php if(isset($isPurchasingUser) && $isPurchasingUser): ?>
                                                    <!-- For purchasing users, show only requirements indicator -->
                                                    <?php if($lead->notes): ?>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 00-.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                        <span class="text-indigo-600 font-medium">Requirements Available</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <?php if($lead->phone || $lead->mobile): ?>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                        </svg>
                                                    <?php endif; ?>
                                                    <?php if($lead->email): ?>
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                
                                                <!-- Assigned User -->
                                                <?php if($lead->assignedUser): ?>
                                                    <div class="flex items-center space-x-1 ml-2">
                                                        <svg class="w-3 h-3 <?php echo e($lead->assignedUser->id === Auth::id() ? 'text-green-500' : 'text-blue-500'); ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                        <?php if($lead->assignedUser->id === Auth::id()): ?>
                                                            <span class="text-green-600 font-bold" title="<?php echo e($lead->assignedUser->name); ?> (You)">
                                                                <?php echo e($lead->assignedUser->name); ?>

                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-blue-600 font-medium" title="<?php echo e($lead->assignedUser->name); ?>">
                                                                <?php echo e($lead->assignedUser->name); ?>

                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Age Since Creation (days hours minutes) -->
                                            <span class="text-gray-400 font-medium" title="<?php echo e($lead->created_at->format('Y-m-d H:i')); ?>">
                                                <?php echo e($lead->created_ago); ?>

                                            </span>
                                        </div>

                                        <!-- Source Row -->
                                        <div class="flex items-center justify-end text-xs text-gray-500">
                                            <!-- Source -->
                                            <span class="capitalize text-<?php echo e($stage['color']); ?>-600 font-medium">
                                                <?php switch($lead->source):
                                                    case ('whatsapp'): ?>
                                                        📱 WhatsApp
                                                        <?php break; ?>
                                                    <?php case ('email'): ?>
                                                        📧 Email
                                                        <?php break; ?>
                                                    <?php case ('website'): ?>
                                                        🌐 Web
                                                        <?php break; ?>
                                                    <?php case ('phone'): ?>
                                                        ☎️ Phone
                                                        <?php break; ?>
                                                    <?php case ('linkedin'): ?>
                                                        💼 LinkedIn
                                                        <?php break; ?>
                                                    <?php default: ?>
                                                        <?php echo e(ucfirst(str_replace('_', ' ', $lead->source))); ?>

                                                <?php endswitch; ?>
                                            </span>
                                        </div>

                                        <!-- Overdue Indicator -->
                                        <?php if($lead->isOverdue()): ?>
                                            <div class="mt-2 flex items-center text-xs text-red-600">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Overdue (<?php echo e((int) floor($lead->daysSinceLastContact())); ?> days)
                                            </div>
                                        <?php endif; ?>

                                        <!-- Quick Action Buttons (Hidden by default, shown on hover) -->
                                        <div class="mt-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                <?php if(isset($isPurchasingUser) && $isPurchasingUser): ?>
                                                    <!-- For purchasing users, show requirements and submit price buttons -->
                                                    <div class="flex space-x-1 w-full">
                                                    <?php if($lead->notes): ?>
                                                        <button onclick="event.stopPropagation(); viewLeadRequirements(<?php echo e($lead->id); ?>)" 
                                                            class="flex-1 text-xs py-1 px-2 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors"
                                                                title="View Requirements">
                                                            Requirements
                                                        </button>
                                                    <?php endif; ?>
                                                        <button onclick="event.stopPropagation(); openPriceSubmissionModal(<?php echo e($lead->id); ?>)" 
                                                            class="flex-1 text-xs py-1 px-2 bg-green-100 text-green-700 rounded hover:bg-green-200 transition-colors"
                                                                title="Submit Price">
                                                            Submit Price
                                                        </button>
                                                        <?php if($lead->hasPriceSubmissions()): ?>
                                                            <button onclick="event.stopPropagation(); viewPriceSubmissions(<?php echo e($lead->id); ?>)" 
                                                                class="flex-1 text-xs py-1 px-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                                                                    title="View Price Submissions">
                                                                Prices
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('crm.leads.edit')): ?>
                                                    <button onclick="event.stopPropagation(); window.location.href='/crm/leads/<?php echo e($lead->id); ?>/edit'" 
                                                        class="flex-1 text-xs py-1 px-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors"
                                                            title="Edit Lead">
                                                        Edit
                                                    </button>
                                                    <?php endif; ?>
                                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('crm.leads.view_requirements')): ?>
                                                    <button onclick="event.stopPropagation(); viewLeadRequirements(<?php echo e($lead->id); ?>)" 
                                                        class="flex-1 text-xs py-1 px-2 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors"
                                                            title="View Requirements">
                                                        Requirements
                                                    </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Hover Effect -->
                                    <div class="absolute inset-0 bg-<?php echo e($stage['color']); ?>-50 opacity-0 group-hover:opacity-20 transition-opacity duration-200 rounded-lg pointer-events-none"></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <!-- Enhanced Empty State -->
                                <div class="flex flex-col items-center justify-center py-16 text-gray-500">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-700 mb-2">No leads in <?php echo e($stage['title']); ?></p>
                                    <p class="text-xs text-gray-500 text-center max-w-40">Leads will appear here when they reach this stage</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- Enhanced No Data State -->
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No leads found</h3>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">Get started by creating your first lead and watch your sales pipeline come to life.</p>
                    <a href="<?php echo e(route('crm.leads.create')); ?>" 
                       class="inline-flex items-center px-6 py-3 border border-transparent shadow-lg text-base font-medium rounded-xl text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform hover:scale-105 transition-all duration-200">
                        <svg class="-ml-1 mr-3 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Your First Lead
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Function to view lead requirements for purchasing employees
function viewLeadRequirements(leadId) {
    fetch(`/crm/leads/${leadId}/requirements`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Create a modal to display the requirements
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            modal.innerHTML = `
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Lead Requirements</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lead ID</label>
                                <p class="text-sm text-gray-900">#${data.lead_id}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Estimated Value</label>
                                <p class="text-sm text-gray-900">AED ${data.estimated_value ? data.estimated_value.toLocaleString() : 'Not specified'}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <p class="text-sm text-gray-900">${data.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Requirements</label>
                                <div class="mt-1 p-3 bg-gray-50 rounded-md">
                                    <div class="text-sm text-gray-900 formatted-content">${data.requirements ? data.requirements.replace(/\n/g, '<br>') : 'No requirements specified'}</div>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Created: ${new Date(data.created_at).toLocaleDateString()}</span>
                                <span>Updated: ${new Date(data.updated_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end">
                            <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            console.error('Failed to load lead requirements:', data.message || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error loading lead requirements:', error);
    });
}

// Function to open price submission modal
function openPriceSubmissionModal(leadId) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-10 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Submit Price</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form id="priceSubmissionForm" class="space-y-4" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                            <input type="number" name="price" step="0.01" min="0" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency *</label>
                            <select name="currency" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="AED">AED</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" placeholder="Additional notes about the pricing..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                        <input type="file" name="attachments[]" multiple 
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp,.xls,.xlsx"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Max 10MB per file. Supported: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="this.closest('.fixed').remove()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Submit Price
                        </button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Handle form submission
    const form = document.getElementById('priceSubmissionForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        submitButton.textContent = 'Submitting...';
        submitButton.disabled = true;
        
        const formData = new FormData(form);
        
        fetch(`/crm/leads/${leadId}/price-submissions`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                modal.remove();
                showNotification('Price submitted successfully!', 'success');
                // Refresh the page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to submit price', 'error');
            }
        })
        .catch(error => {
            console.error('Error submitting price:', error);
            showNotification('Failed to submit price. Please try again.', 'error');
        })
        .finally(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    });
}

// Function to view price submissions
function viewPriceSubmissions(leadId) {
    fetch(`/crm/leads/${leadId}/price-submissions`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
            
            let priceSubmissionsHtml = '';
            if (data.price_submissions && data.price_submissions.length > 0) {
                priceSubmissionsHtml = data.price_submissions.map(submission => {
                    const attachmentsHtml = submission.attachments && submission.attachments.length > 0 
                        ? `<div class="mt-2">
                            <p class="text-xs font-medium text-gray-600 mb-1">Attachments:</p>
                            <div class="flex flex-wrap gap-1">
                                ${submission.attachments.map((attachment, index) => 
                                    `<a href="/crm/price-submissions/${submission.id}/attachments/${index}" 
                                        class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-md hover:bg-blue-200" 
                                        title="Download ${attachment.original_name}">
                                        📎 ${attachment.original_name}
                                    </a>`
                                ).join('')}
                            </div>
                        </div>`
                        : '';
                    
                    return `
                        <div class="border border-gray-200 rounded-lg p-4 mb-3">
                            <div class="flex justify-between items-start mb-2">
                                <div class="text-lg font-semibold text-green-600">
                                    ${submission.currency} ${parseFloat(submission.price).toLocaleString()}
                                </div>
                                <div class="text-xs text-gray-500">
                                    ${new Date(submission.created_at).toLocaleDateString()}
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mb-1">
                                Submitted by: <span class="font-medium">${submission.user.name}</span>
                            </div>
                            ${submission.notes ? `<div class="text-sm text-gray-700 bg-gray-50 p-2 rounded">${submission.notes}</div>` : ''}
                            ${attachmentsHtml}
                        </div>
                    `;
                }).join('');
            } else {
                priceSubmissionsHtml = '<p class="text-gray-500 text-center py-4">No price submissions yet.</p>';
            }
            
            modal.innerHTML = `
                <div class="relative top-10 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Price Submissions</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto">
                            ${priceSubmissionsHtml}
                        </div>
                        
                        <div class="flex justify-end pt-4">
                            <button onclick="this.closest('.fixed').remove()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        } else {
            showNotification('Failed to load price submissions', 'error');
        }
    })
    .catch(error => {
        console.error('Error loading price submissions:', error);
        showNotification('Failed to load price submissions', 'error');
    });
}

// Notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
        type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
        'bg-blue-100 text-blue-800 border border-blue-200'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-1">${message}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-gray-500 hover:text-gray-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script> <?php /**PATH C:\Users\Walid\OneDrive\Desktop\MaxMed\resources\views/crm/leads/partials/pipeline-view.blade.php ENDPATH**/ ?>