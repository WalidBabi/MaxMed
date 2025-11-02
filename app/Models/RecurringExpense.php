<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vendor',
        'unit_amount',
        'quantity',
        'currency',
        'frequency',
        'repeats_every',
        'active_months_mask',
        'start_date',
        'end_date',
        'next_due_date',
        'status',
        'is_installment',
        'notes',
    ];

    protected $casts = [
        'unit_amount' => 'decimal:2',
        'quantity' => 'integer',
        'repeats_every' => 'integer',
        'active_months_mask' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_due_date' => 'date',
    ];

    public const FREQUENCY_MONTHLY = 'monthly';
    public const FREQUENCY_YEARLY = 'yearly';
    public const FREQUENCY_QUARTERLY = 'quarterly';
    public const FREQUENCY_WEEKLY = 'weekly';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_ENDED = 'ended';

    public function getTotalAmountAttribute(): string
    {
        $amount = (float) $this->unit_amount * (int) $this->quantity;
        return number_format($amount, 2, '.', '');
    }

    public function isActiveInMonth(int $month): bool
    {
        // month: 1-12
        $mask = (int) $this->active_months_mask;
        if ($mask === 0) {
            return true; // treat 0 as all months active for convenience
        }
        $bit = 1 << ($month - 1);
        return (bool) ($mask & $bit);
    }

    public function activeMonths(): array
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            if ($this->isActiveInMonth($i)) {
                $months[] = $i;
            }
        }
        return $months;
    }

    public function payments()
    {
        return $this->hasMany(ExpensePayment::class, 'recurring_expense_id');
    }

    public function isPaidForMonth(int $year, int $month): bool
    {
        $period = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        return $this->payments()->whereDate('period_date', $period)->exists();
    }

    public function totalInstallmentMonths(): int
    {
        $mask = (int) $this->active_months_mask;
        if ($mask === 0) {
            return 12;
        }
        $count = 0;
        for ($i = 1; $i <= 12; $i++) {
            if ($mask & (1 << ($i - 1))) {
                $count++;
            }
        }
        return $count;
    }

    public function paidInstallmentsInYear(int $year): int
    {
        return $this->payments()->whereYear('period_date', $year)->count();
    }

    public function nextDueAfter(Carbon $fromDate): ?Carbon
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return null;
        }

        $start = $this->next_due_date ? Carbon::parse($this->next_due_date) : ($this->start_date ? Carbon::parse($this->start_date) : now());
        $date = $start->copy();

        // advance until after fromDate and on an active month
        $safety = 0;
        while ($date->lessThanOrEqualTo($fromDate) || !$this->isActiveInMonth((int) $date->format('n'))) {
            $safety++;
            if ($safety > 240) { // prevent infinite loop
                return null;
            }

            switch ($this->frequency) {
                case self::FREQUENCY_WEEKLY:
                    $date->addWeeks(max(1, (int) $this->repeats_every));
                    break;
                case self::FREQUENCY_QUARTERLY:
                    $date->addMonths(3 * max(1, (int) $this->repeats_every));
                    break;
                case self::FREQUENCY_YEARLY:
                    $date->addYears(max(1, (int) $this->repeats_every));
                    break;
                case self::FREQUENCY_MONTHLY:
                default:
                    $date->addMonths(max(1, (int) $this->repeats_every));
                    break;
            }

            if ($this->end_date && $date->greaterThan(Carbon::parse($this->end_date))) {
                return null;
            }
        }

        return $date;
    }
}


