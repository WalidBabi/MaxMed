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
            return redirect()->back()->with('error', 'This expense is not active for the selected month.');
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

        return redirect()->back()->with('success', 'Marked as paid for ' . $period->format('M Y'));
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
        foreach ($expenses as $exp) {
            if ($exp->isActiveInMonth((int) $now->format('n'))) {
                $thisMonth += (float) $exp->unit_amount * (int) $exp->quantity;
            }
            $nm = $now->copy()->addMonth();
            if ($exp->isActiveInMonth((int) $nm->format('n'))) {
                $nextMonth += (float) $exp->unit_amount * (int) $exp->quantity;
            }
        }
        return [
            'this_month_total' => number_format($thisMonth, 2, '.', ''),
            'next_month_total' => number_format($nextMonth, 2, '.', ''),
        ];
    }
}
