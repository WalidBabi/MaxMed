<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'recurring_expense_id',
        'period_date',
        'amount',
        'currency',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_date' => 'date',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function expense()
    {
        return $this->belongsTo(RecurringExpense::class, 'recurring_expense_id');
    }
}


