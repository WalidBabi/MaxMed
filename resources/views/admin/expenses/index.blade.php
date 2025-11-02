@extends('admin.layouts.app')

@section('title', 'Business Expenses')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Business Expenses</h1>
        <p class="text-gray-600">Manage recurring company expenses. Superadmin only.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.business-expenses.forecast') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Forecast</a>
        <a href="{{ route('admin.business-expenses.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Add Expense</a>
    </div>
    </div>

    <!-- Enhanced KPI Section -->
    <div class="mb-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-4">
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Current Period</div>
                <div class="text-sm font-medium text-gray-700 mb-1">{{ $kpis['current_month_name'] }}</div>
                <div class="text-xs text-gray-500 mb-3">{{ $kpis['current_month_range'] }}</div>
                <div class="text-3xl font-bold text-indigo-600">{{ $kpis['this_month_total'] }} <span class="text-lg text-gray-500">AED</span></div>
                <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Paid:</span>
                        <span class="font-semibold text-green-600">{{ number_format((float)$kpis['this_month_paid'], 2) }} AED</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Unpaid:</span>
                        <span class="font-semibold text-orange-600">{{ number_format((float)$kpis['this_month_unpaid'], 2) }} AED</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Expenses:</span>
                        <span class="font-semibold text-gray-700">{{ $kpis['this_month_expenses_count'] }} items</span>
                    </div>
                </div>
            </div>
            
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Next Period</div>
                <div class="text-sm font-medium text-gray-700 mb-1">{{ $kpis['next_month_name'] }}</div>
                <div class="text-xs text-gray-500 mb-3">{{ $kpis['next_month_range'] }}</div>
                <div class="text-3xl font-bold text-blue-600">{{ $kpis['next_month_total'] }} <span class="text-lg text-gray-500">AED</span></div>
                <div class="mt-3 pt-3 border-t border-gray-100 space-y-1">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Paid:</span>
                        <span class="font-semibold text-green-600">{{ number_format((float)$kpis['next_month_paid'], 2) }} AED</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Unpaid:</span>
                        <span class="font-semibold text-orange-600">{{ number_format((float)$kpis['next_month_unpaid'], 2) }} AED</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Expenses:</span>
                        <span class="font-semibold text-gray-700">{{ $kpis['next_month_expenses_count'] }} items</span>
                    </div>
                </div>
            </div>
            
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">This Month - By Frequency</div>
                <div class="mt-3 space-y-2">
                    @foreach($kpis['this_month_by_frequency'] as $freq => $amount)
                        @if($amount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 capitalize">{{ $freq }}:</span>
                                <span class="font-semibold text-gray-900">{{ number_format((float)$amount, 2) }} AED</span>
                            </div>
                        @endif
                    @endforeach
                    @if(array_sum($kpis['this_month_by_frequency']) == 0)
                        <div class="text-xs text-gray-400 italic">No expenses this month</div>
                    @endif
                </div>
            </div>
            
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="text-xs text-gray-400 uppercase tracking-wide mb-1">Next Month - By Frequency</div>
                <div class="mt-3 space-y-2">
                    @foreach($kpis['next_month_by_frequency'] as $freq => $amount)
                        @if($amount > 0)
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 capitalize">{{ $freq }}:</span>
                                <span class="font-semibold text-gray-900">{{ number_format((float)$amount, 2) }} AED</span>
                            </div>
                        @endif
                    @endforeach
                    @if(array_sum($kpis['next_month_by_frequency']) == 0)
                        <div class="text-xs text-gray-400 italic">No expenses next month</div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Detailed Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">This Month Expenses Breakdown</h3>
                    <span class="text-xs text-gray-500">{{ $kpis['this_month_expenses_count'] }} items</span>
                </div>
                @if(count($kpis['this_month_expenses']) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($kpis['this_month_expenses'] as $expense)
                            <div class="flex items-center justify-between p-2 rounded-md bg-gray-50 hover:bg-gray-100">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense['name'] }}</div>
                                    <div class="text-xs text-gray-500 capitalize">{{ $expense['frequency'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format((float)$expense['amount'], 2) }} AED</div>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $expense['paid'] ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $expense['paid'] ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-400 italic text-center py-4">No expenses for this month</div>
                @endif
            </div>
            
            <div class="rounded-lg bg-white p-5 shadow">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-900">Next Month Expenses Breakdown</h3>
                    <span class="text-xs text-gray-500">{{ $kpis['next_month_expenses_count'] }} items</span>
                </div>
                @if(count($kpis['next_month_expenses']) > 0)
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($kpis['next_month_expenses'] as $expense)
                            <div class="flex items-center justify-between p-2 rounded-md bg-gray-50 hover:bg-gray-100">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">{{ $expense['name'] }}</div>
                                    <div class="text-xs text-gray-500 capitalize">{{ $expense['frequency'] }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format((float)$expense['amount'], 2) }} AED</div>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $expense['paid'] ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ $expense['paid'] ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-sm text-gray-400 italic text-center py-4">No expenses for next month</div>
                @endif
            </div>
        </div>
        
        <!-- Summary Stats -->
        <div class="mt-4 rounded-lg bg-gradient-to-r from-indigo-50 to-blue-50 p-4 border border-indigo-100">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span class="font-semibold">{{ $kpis['total_active_expenses'] }}</span> active expense{{ $kpis['total_active_expenses'] != 1 ? 's' : '' }} in system
                </div>
                <div class="text-sm text-gray-600">
                    <span class="font-semibold">{{ number_format((float)$kpis['this_month_total'] + (float)$kpis['next_month_total'], 2) }}</span> AED total for both months
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-lg bg-white shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frequency</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">This Month</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $expense->name }}
                            @php
                                $now = \Illuminate\Support\Carbon::now()->startOfDay();
                                $currentYear = (int) $now->format('Y');
                                $currentMonth = (int) $now->format('n');
                                $isPaidForCurrentMonth = $expense->isActiveInMonth($currentMonth) && $expense->isPaidForMonth($currentYear, $currentMonth);
                                
                                // Initialize variables
                                $deadline = null; // Next installment deadline
                                $overallDeadline = null; // Overall license deadline
                                $label = '';
                                $isPastDue = false;
                                $daysRemaining = 0;
                                $daysOverdue = 0;
                                $isDueSoon = false;
                                $progressPercent = 0;
                                
                                // For installment expenses, calculate next installment deadline
                                if ($expense->is_installment) {
                                    $activeMonths = $expense->activeMonths();
                                    
                                    // Find next active month
                                    $nextInstallmentMonth = null;
                                    $nextInstallmentYear = $currentYear;
                                    
                                    // Check if current month is active and paid or not due
                                    if ($expense->isActiveInMonth($currentMonth)) {
                                        if ($isPaidForCurrentMonth) {
                                            // Current month is paid, find next active month
                                            foreach ($activeMonths as $monthNum) {
                                                if ($monthNum > $currentMonth) {
                                                    $nextInstallmentMonth = $monthNum;
                                                    break;
                                                }
                                            }
                                            // If no month found in current year, check next year
                                            if (!$nextInstallmentMonth && !empty($activeMonths)) {
                                                $nextInstallmentMonth = min($activeMonths);
                                                $nextInstallmentYear = $currentYear + 1;
                                            }
                                        } else {
                                            // Current month is active but not paid - use current month
                                            $nextInstallmentMonth = $currentMonth;
                                        }
                                    } else {
                                        // Current month is not active, find next active month
                                        foreach ($activeMonths as $monthNum) {
                                            if ($monthNum >= $currentMonth) {
                                                $nextInstallmentMonth = $monthNum;
                                                break;
                                            }
                                        }
                                        // If no month found in current year, check next year
                                        if (!$nextInstallmentMonth && !empty($activeMonths)) {
                                            $nextInstallmentMonth = min($activeMonths);
                                            $nextInstallmentYear = $currentYear + 1;
                                        }
                                    }
                                    
                                    if ($nextInstallmentMonth) {
                                        $deadline = \Illuminate\Support\Carbon::create($nextInstallmentYear, $nextInstallmentMonth, 1)->endOfMonth();
                                        $label = 'Next Installment Due';
                                    }
                                    
                                    // Overall license deadline (from next_due_date)
                                    if ($expense->next_due_date) {
                                        $overallDeadline = \Illuminate\Support\Carbon::parse($expense->next_due_date)->startOfDay();
                                    }
                                } else {
                                    // Non-installment expenses: use existing logic
                                    if ($isPaidForCurrentMonth) {
                                        // If already paid, prefer using the existing next_due_date
                                        // Only calculate a new date if next_due_date is not set
                                        if ($expense->next_due_date) {
                                            $deadline = \Illuminate\Support\Carbon::parse($expense->next_due_date)->startOfDay();
                                            $label = 'Next Payment Due';
                                        } else {
                                            // Calculate next payment date based on frequency only if next_due_date is not set
                                            $nextPaymentDate = null;
                                            switch ($expense->frequency) {
                                                case \App\Models\RecurringExpense::FREQUENCY_MONTHLY:
                                                    // For monthly, add 1 month to the current date (preserve day if possible)
                                                    $nextPaymentDate = $now->copy()->addMonth();
                                                    break;
                                                case \App\Models\RecurringExpense::FREQUENCY_YEARLY:
                                                    if ($expense->start_date) {
                                                        $startDate = \Illuminate\Support\Carbon::parse($expense->start_date);
                                                        $nextPaymentDate = \Illuminate\Support\Carbon::create($currentYear, $startDate->month, $startDate->day);
                                                        if ($nextPaymentDate->isPast()) {
                                                            $nextPaymentDate->addYear();
                                                        }
                                                    } else {
                                                        $nextPaymentDate = $now->copy()->addYear();
                                                    }
                                                    break;
                                                case \App\Models\RecurringExpense::FREQUENCY_QUARTERLY:
                                                    // For quarterly, add 3 months (preserve day if possible)
                                                    $nextPaymentDate = $now->copy()->addMonths(3);
                                                    break;
                                                case \App\Models\RecurringExpense::FREQUENCY_WEEKLY:
                                                    // For weekly, add 1 month (preserve day if possible)
                                                    $nextPaymentDate = $now->copy()->addMonth();
                                                    break;
                                                default:
                                                    $nextPaymentDate = $now->copy()->addMonth();
                                                    break;
                                            }
                                            
                                            $deadline = $nextPaymentDate;
                                            $label = 'Next Payment Due';
                                        }
                                    } elseif ($expense->next_due_date) {
                                        $deadline = \Illuminate\Support\Carbon::parse($expense->next_due_date)->startOfDay();
                                        $label = 'Payment Deadline';
                                    }
                                }
                                
                                // Calculate deadline details and progress
                                if ($deadline) {
                                    $isPastDue = $deadline->isPast();
                                    $daysRemaining = $isPastDue ? 0 : $now->diffInDays($deadline, false);
                                    $daysRemainingDisplay = (int) max(0, ceil($daysRemaining));
                                    $daysOverdue = $isPastDue ? $deadline->diffInDays($now, false) : 0;
                                    $isDueSoon = !$isPastDue && $daysRemaining <= 30;
                                    
                                    // Calculate progress: for installment, show progress to next installment
                                    // For yearly, show progress towards deadline (assume 365-day cycle)
                                    // For other frequencies, use 30 days as base
                                    if ($expense->is_installment) {
                                        // For installments: progress from start of current month to next installment
                                        $monthStart = $now->copy()->startOfMonth();
                                        $monthEnd = $deadline->copy()->endOfMonth();
                                        $monthLength = max(1, $monthStart->diffInDays($monthEnd, false));
                                        $daysElapsed = max(0, $monthStart->diffInDays($now, false));
                                        $progressPercent = min(100, max(0, ($daysElapsed / $monthLength) * 100));
                                    } else {
                                        $totalDays = $expense->frequency === \App\Models\RecurringExpense::FREQUENCY_YEARLY ? 365 : 30;
                                        if ($isPaidForCurrentMonth) {
                                            // When current month is paid, show progress from start of current cycle to next payment
                                            $cycleStart = $now->copy()->startOfMonth();
                                            $cycleEnd = $deadline->copy()->startOfMonth();
                                            $cycleLength = max(1, $cycleStart->diffInDays($cycleEnd, false));
                                            $daysElapsed = max(0, $cycleStart->diffInDays($now, false));
                                            $progressPercent = min(100, max(0, ($daysElapsed / $cycleLength) * 100));
                                        } elseif ($expense->start_date) {
                                            $startDate = \Illuminate\Support\Carbon::parse($expense->start_date)->startOfDay();
                                            $daysSinceStart = max(0, $startDate->diffInDays($now, false));
                                            $progressPercent = min(100, max(0, ($daysSinceStart / $totalDays) * 100));
                                        } else {
                                            // Fallback: show progress based on days remaining
                                            $totalDaysUntilDeadline = max(1, $now->diffInDays($deadline, false));
                                            $progressPercent = $isPastDue ? 100 : min(100, max(0, (($totalDays - $totalDaysUntilDeadline) / $totalDays) * 100));
                                        }
                                    }
                                }

                                // Also compute overall license deadline progress if available (for installments)
                                $overallIsPastDue = false;
                                $overallDaysRemaining = 0;
                                $overallIsDueSoon = false;
                                $overallProgressPercent = 0;
                                if ($overallDeadline) {
                                    $overallIsPastDue = $overallDeadline->isPast();
                                    $overallDaysRemaining = $overallIsPastDue ? 0 : $now->diffInDays($overallDeadline, false);
                                    $overallDaysRemainingDisplay = (int) max(0, ceil($overallDaysRemaining));
                                    $overallIsDueSoon = !$overallIsPastDue && $overallDaysRemaining <= 30;
                                    // Assume yearly cycle for license expirations (365 days window)
                                    $overallTotalDays = 365;
                                    $overallProgressPercent = $overallIsPastDue
                                        ? 100
                                        : min(100, max(0, (($overallTotalDays - min($overallTotalDays, $overallDaysRemaining)) / $overallTotalDays) * 100));
                                }
                            @endphp
                            @if($deadline)
                                <div class="mt-2 space-y-1">
                                    <div class="text-xs font-medium {{ $isPastDue ? 'text-red-600' : ($isDueSoon ? 'text-orange-600' : 'text-gray-600') }}">
                                        {{ $label }}: {{ $deadline->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $isPastDue ? 'bg-red-500' : ($isDueSoon ? 'bg-orange-500' : 'bg-blue-500') }}" 
                                                 style="width: {{ $progressPercent }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold {{ $isPastDue ? 'text-red-600' : ($isDueSoon ? 'text-orange-600' : 'text-gray-600') }} whitespace-nowrap">
                                            @if($isPastDue)
                                                Overdue by {{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }}
                                            @else
                                                {{ $daysRemainingDisplay }} day{{ $daysRemainingDisplay != 1 ? 's' : '' }} left
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if($overallDeadline && $expense->is_installment)
                                <div class="mt-2 space-y-1">
                                    <div class="text-xs font-medium {{ $overallIsPastDue ? 'text-red-600' : ($overallIsDueSoon ? 'text-orange-600' : 'text-gray-600') }}">
                                        License Expiration: {{ $overallDeadline->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $overallIsPastDue ? 'bg-red-500' : ($overallIsDueSoon ? 'bg-purple-500' : 'bg-gray-500') }}" 
                                                 style="width: {{ $overallProgressPercent }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold {{ $overallIsPastDue ? 'text-red-600' : ($overallIsDueSoon ? 'text-purple-600' : 'text-gray-600') }} whitespace-nowrap">
                                            @if($overallIsPastDue)
                                                Overdue by {{ $overallDeadline->diffInDays($now) }} day{{ $overallDeadline->diffInDays($now) != 1 ? 's' : '' }}
                                            @else
                                                {{ $overallDaysRemainingDisplay }} day{{ $overallDaysRemainingDisplay != 1 ? 's' : '' }} left
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $expense->vendor }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($expense->unit_amount * $expense->quantity, 2) }} {{ $expense->currency }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($expense->frequency) }} @if($expense->repeats_every > 1) (every {{ $expense->repeats_every }}) @endif</td>
                        <td class="px-4 py-3 text-sm">
                            @if($expense->is_installment)
                                <span class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800">Installment</span>
                                @php
                                    $year = now()->year;
                                    $paid = $expense->paidInstallmentsInYear($year);
                                    $total = $expense->totalInstallmentMonths();
                                @endphp
                                <div class="text-xs text-gray-500 mt-1">{{ $paid }} / {{ $total }} paid</div>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">Recurring</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php $now = \Illuminate\Support\Carbon::now(); @endphp
                            @if($expense->isActiveInMonth((int) $now->format('n')))
                                @if($expense->isPaidForMonth((int)$now->format('Y'), (int)$now->format('n')))
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Paid</span>
                                @else
                                    <form method="POST" action="{{ route('admin.business-expenses.mark-paid', $expense) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="year" value="{{ $now->format('Y') }}">
                                        <input type="hidden" name="month" value="{{ $now->format('n') }}">
                                        <button type="submit" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Mark as paid</button>
                                    </form>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">Not due</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @php
                                $months = $expense->activeMonths();
                                $isEveryMonth = $expense->frequency === \App\Models\RecurringExpense::FREQUENCY_MONTHLY && 
                                               $expense->active_months_mask === 0 && 
                                               $expense->repeats_every == 1;
                            @endphp
                            @if($isEveryMonth)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Every month
                                </span>
                            @elseif(empty($months) || count($months) == 12)
                                <span class="text-gray-600">All months</span>
                            @else
                                <span class="text-gray-600">{{ implode(', ', array_map(fn($m) => date('M', mktime(0,0,0,$m,1)), $months)) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.business-expenses.edit', $expense) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                            <form action="{{ route('admin.business-expenses.destroy', $expense) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ml-3 text-red-600 hover:text-red-800 text-sm" onclick="return confirm('Delete this expense?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">No expenses yet. Click "Add Expense" to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3">{{ $expenses->links() }}</div>
    </div>
@endsection


