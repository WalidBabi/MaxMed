<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringExpense;
use App\Models\ExpensePayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BusinessExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);
    }

    /**
     * Ensure user is superadmin
     */
    protected function checkSuperAdmin()
    {
        $user = auth()->user();
        if (!$user || !($user->hasRole('super_admin') || $user->hasRole('superadmin') || $user->hasRole('super-administrator'))) {
            abort(403, 'Only super administrators can access business expenses.');
        }
    }

    public function index(Request $request)
    {
        $this->checkSuperAdmin();
        $expenses = RecurringExpense::orderBy('name')->paginate(20);
        $kpis = $this->computeKpis();
        return view('admin.expenses.index', compact('expenses', 'kpis'));
    }

    public function create()
    {
        $this->checkSuperAdmin();
        return view('admin.expenses.create');
    }

    public function store(Request $request)
    {
        $this->checkSuperAdmin();
        $data = $this->validateData($request);
        RecurringExpense::create($data);
        return redirect()->route('admin.business-expenses.index')->with('success', 'Expense created successfully');
    }

    public function edit(RecurringExpense $business_expense)
    {
        $this->checkSuperAdmin();
        return view('admin.expenses.edit', ['expense' => $business_expense]);
    }

    public function update(Request $request, RecurringExpense $business_expense)
    {
        $this->checkSuperAdmin();
        $data = $this->validateData($request);
        $business_expense->update($data);
        return redirect()->route('admin.business-expenses.index')->with('success', 'Expense updated successfully');
    }

    public function destroy(RecurringExpense $business_expense)
    {
        $this->checkSuperAdmin();
        $business_expense->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense deleted',
                'expense_id' => $business_expense->id,
            ]);
        }

        return redirect()->route('admin.business-expenses.index')->with('success', 'Expense deleted');
    }

    public function show(RecurringExpense $business_expense)
    {
        $this->checkSuperAdmin();
        return redirect()->route('admin.business-expenses.edit', $business_expense);
    }

    public function forecast(Request $request)
    {
        $this->checkSuperAdmin();
        $monthsAhead = (int) ($request->integer('months', 12));
        $start = Carbon::now()->startOfMonth();
        $expenses = RecurringExpense::where('status', RecurringExpense::STATUS_ACTIVE)->get();

        $forecast = [];
        for ($i = 0; $i < $monthsAhead; $i++) {
            $monthDate = $start->copy()->addMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $forecast[$monthKey] = [
                'date' => $monthDate->copy(),
                'items' => [],
                'total' => 0.0,
            ];
            foreach ($expenses as $exp) {
                if ($exp->isActiveInMonth((int) $monthDate->format('n'))) {
                    // Basic frequency filter: include monthly; yearly if same month; quarterly every 3 months
                    $include = false;
                    switch ($exp->frequency) {
                        case RecurringExpense::FREQUENCY_WEEKLY:
                            $include = true; // simplified: show as monthly item
                            break;
                        case RecurringExpense::FREQUENCY_QUARTERLY:
                            $include = ($monthDate->month % 3) === (($exp->start_date ? Carbon::parse($exp->start_date)->month : 1) % 3);
                            break;
                        case RecurringExpense::FREQUENCY_YEARLY:
                            $include = $exp->start_date ? ((int) $monthDate->format('n') === (int) Carbon::parse($exp->start_date)->format('n')) : false;
                            break;
                        case RecurringExpense::FREQUENCY_MONTHLY:
                        default:
                            $include = true;
                            break;
                    }

                    if ($include) {
                        $amount = (float) $exp->unit_amount * (int) $exp->quantity;
                        $forecast[$monthKey]['items'][] = [
                            'name' => $exp->name,
                            'vendor' => $exp->vendor,
                            'amount' => $amount,
                            'currency' => $exp->currency,
                        ];
                        $forecast[$monthKey]['total'] += $amount;
                    }
                }
            }
        }

        return view('admin.expenses.forecast', compact('forecast'));
    }

    public function markPaid(Request $request, RecurringExpense $business_expense)
    {
        $this->checkSuperAdmin();
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        // Only allow marking paid for active months
        if (!$business_expense->isActiveInMonth($month)) {
            $message = 'This expense is not active for the selected month.';
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $period = Carbon::create($year, $month, 1)->startOfMonth();
        ExpensePayment::updateOrCreate(
            [
                'recurring_expense_id' => $business_expense->id,
                'period_date' => $period->toDateString(),
            ],
            [
                'amount' => (float) $business_expense->unit_amount * (int) $business_expense->quantity,
                'currency' => $business_expense->currency,
                'status' => 'paid',
                'paid_at' => now(),
            ]
        );

        $message = 'Marked as paid for ' . $period->format('M Y');

        if ($request->wantsJson()) {
            $business_expense->refresh();

            $statusHtml = view('admin.expenses.partials.status-cell', [
                'expense' => $business_expense,
            ])->render();

            return response()->json([
                'success' => true,
                'message' => $message,
                'expense_id' => $business_expense->id,
                'status_html' => $statusHtml,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    protected function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'unit_amount' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'currency' => ['required', 'string', 'max:10'],
            'frequency' => ['required', Rule::in([
                RecurringExpense::FREQUENCY_MONTHLY,
                RecurringExpense::FREQUENCY_YEARLY,
                RecurringExpense::FREQUENCY_QUARTERLY,
                RecurringExpense::FREQUENCY_WEEKLY,
            ])],
            'repeats_every' => ['required', 'integer', 'min:1'],
            'active_months_mask' => ['nullable', 'integer', 'min:0', 'max:4095'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'next_due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in([
                RecurringExpense::STATUS_ACTIVE,
                RecurringExpense::STATUS_PAUSED,
                RecurringExpense::STATUS_ENDED,
            ])],
            'is_installment' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        // default mask if not provided
        if (!isset($validated['active_months_mask'])) {
            $validated['active_months_mask'] = 0; // all months
        }
        $validated['is_installment'] = (bool) ($validated['is_installment'] ?? false);

        return $validated;
    }

    protected function computeKpis(): array
    {
        $now = Carbon::now();
        $expenses = RecurringExpense::where('status', RecurringExpense::STATUS_ACTIVE)->get();
        $thisMonth = 0.0;
        $nextMonth = 0.0;
        
        $thisMonthDate = $now->copy()->startOfMonth();
        $nextMonthDate = $now->copy()->addMonth()->startOfMonth();
        $currentYear = (int) $now->format('Y');
        $currentMonthNum = (int) $now->format('n');
        
        // Breakdowns for this month
        $thisMonthPaid = 0.0;
        $thisMonthUnpaid = 0.0;
        $thisMonthByFrequency = [
            'monthly' => 0.0,
            'yearly' => 0.0,
            'quarterly' => 0.0,
            'weekly' => 0.0,
        ];
        $thisMonthExpenses = [];
        
        // Breakdowns for next month
        $nextMonthPaid = 0.0;
        $nextMonthUnpaid = 0.0;
        $nextMonthByFrequency = [
            'monthly' => 0.0,
            'yearly' => 0.0,
            'quarterly' => 0.0,
            'weekly' => 0.0,
        ];
        $nextMonthExpenses = [];
        
        foreach ($expenses as $exp) {
            $amount = (float) $exp->unit_amount * (int) $exp->quantity;
            
            // Check this month
            $canIncludeThisMonth = true;
            if ($exp->start_date && Carbon::parse($exp->start_date)->gt($thisMonthDate->copy()->endOfMonth())) {
                $canIncludeThisMonth = false; // Expense hasn't started yet
            }
            if ($exp->end_date && Carbon::parse($exp->end_date)->lt($thisMonthDate)) {
                $canIncludeThisMonth = false; // Expense has ended
            }
            
            if ($canIncludeThisMonth && $exp->isActiveInMonth($currentMonthNum)) {
                if ($this->shouldIncludeExpenseForMonth($exp, $thisMonthDate)) {
                    $thisMonth += $amount;
                    $isPaidThisMonth = $exp->isPaidForMonth($currentYear, $currentMonthNum);
                    if ($isPaidThisMonth) {
                        $thisMonthPaid += $amount;
                    } else {
                        $thisMonthUnpaid += $amount;
                    }
                    
                    $frequency = $exp->frequency ?? 'monthly';
                    if (isset($thisMonthByFrequency[$frequency])) {
                        $thisMonthByFrequency[$frequency] += $amount;
                    }
                    
                    $thisMonthExpenses[] = [
                        'name' => $exp->name,
                        'amount' => $amount,
                        'frequency' => $frequency,
                        'paid' => $isPaidThisMonth,
                    ];
                }
            }
            
            // Check next month
            $nextMonthNum = (int) $nextMonthDate->format('n');
            $nextYear = (int) $nextMonthDate->format('Y');
            
            $canIncludeNextMonth = true;
            if ($exp->start_date && Carbon::parse($exp->start_date)->gt($nextMonthDate->copy()->endOfMonth())) {
                $canIncludeNextMonth = false; // Expense won't have started yet
            }
            if ($exp->end_date && Carbon::parse($exp->end_date)->lt($nextMonthDate)) {
                $canIncludeNextMonth = false; // Expense will have ended
            }
            
            if ($canIncludeNextMonth && $exp->isActiveInMonth($nextMonthNum)) {
                if ($this->shouldIncludeExpenseForMonth($exp, $nextMonthDate)) {
                    $nextMonth += $amount;
                    $isPaidNextMonth = $exp->isPaidForMonth($nextYear, $nextMonthNum);
                    if ($isPaidNextMonth) {
                        $nextMonthPaid += $amount;
                    } else {
                        $nextMonthUnpaid += $amount;
                    }
                    
                    $frequency = $exp->frequency ?? 'monthly';
                    if (isset($nextMonthByFrequency[$frequency])) {
                        $nextMonthByFrequency[$frequency] += $amount;
                    }
                    
                    $nextMonthExpenses[] = [
                        'name' => $exp->name,
                        'amount' => $amount,
                        'frequency' => $frequency,
                        'paid' => $isPaidNextMonth,
                    ];
                }
            }
        }
        
        return [
            'current_month_name' => $thisMonthDate->format('F Y'),
            'current_month_range' => $thisMonthDate->format('M d') . ' - ' . $thisMonthDate->copy()->endOfMonth()->format('M d, Y'),
            'next_month_name' => $nextMonthDate->format('F Y'),
            'next_month_range' => $nextMonthDate->format('M d') . ' - ' . $nextMonthDate->copy()->endOfMonth()->format('M d, Y'),
            'this_month_total' => number_format($thisMonth, 2, '.', ''),
            'this_month_paid' => number_format($thisMonthPaid, 2, '.', ''),
            'this_month_unpaid' => number_format($thisMonthUnpaid, 2, '.', ''),
            'this_month_by_frequency' => $thisMonthByFrequency,
            'this_month_expenses_count' => count($thisMonthExpenses),
            'this_month_expenses' => $thisMonthExpenses,
            'next_month_total' => number_format($nextMonth, 2, '.', ''),
            'next_month_paid' => number_format($nextMonthPaid, 2, '.', ''),
            'next_month_unpaid' => number_format($nextMonthUnpaid, 2, '.', ''),
            'next_month_by_frequency' => $nextMonthByFrequency,
            'next_month_expenses_count' => count($nextMonthExpenses),
            'next_month_expenses' => $nextMonthExpenses,
            'total_active_expenses' => $expenses->count(),
        ];
    }
    
    /**
     * Check if an expense should be included for a given month based on frequency
     */
    private function shouldIncludeExpenseForMonth($expense, $monthDate): bool
    {
        switch ($expense->frequency) {
            case RecurringExpense::FREQUENCY_MONTHLY:
            case RecurringExpense::FREQUENCY_WEEKLY:
                return true; // Always include monthly/weekly expenses if they're active
            
            case RecurringExpense::FREQUENCY_QUARTERLY:
                if (!$expense->start_date) {
                    return false;
                }
                $startMonth = Carbon::parse($expense->start_date)->month;
                // Check if the month is in the same position within the quarter cycle
                // (e.g., if start is Jan, include Jan, Apr, Jul, Oct)
                $monthsDifference = ($monthDate->month - $startMonth + 12) % 12;
                return $monthsDifference % 3 === 0;
            
            case RecurringExpense::FREQUENCY_YEARLY:
                // For yearly expenses, next_due_date takes precedence if set
                if ($expense->next_due_date) {
                    $dueDate = Carbon::parse($expense->next_due_date);
                    // Check if this month matches the due date month
                    if ($monthDate->year === $dueDate->year && $monthDate->month === $dueDate->month) {
                        return true;
                    }
                }
                // Fallback to start_date if next_due_date not set
                if ($expense->start_date) {
                    $startDate = Carbon::parse($expense->start_date);
                    $startMonth = $startDate->month;
                    // Check if the month matches and the expense has actually started
                    if ((int) $monthDate->format('n') === $startMonth) {
                        if ($monthDate->year > $startDate->year || 
                            ($monthDate->year === $startDate->year && $monthDate->month >= $startDate->month)) {
                            return true;
                        }
                    }
                }
                return false;
            
            default:
                return true;
        }
    }
}
