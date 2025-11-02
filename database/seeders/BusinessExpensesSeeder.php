<?php

namespace Database\Seeders;

use App\Models\RecurringExpense;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BusinessExpensesSeeder extends Seeder
{
    public function run(): void
    {
        // Helper to create or update by name
        $upsert = function(array $data) {
            RecurringExpense::updateOrCreate(['name' => $data['name']], $data);
        };

        // GoDaddy Emails (2 x 66 AED monthly)
        $upsert([
            'name' => 'GoDaddy Email',
            'vendor' => 'GoDaddy',
            'unit_amount' => 66,
            'quantity' => 2,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_MONTHLY,
            'repeats_every' => 1,
            'active_months_mask' => 0, // all months
            'start_date' => now()->startOfMonth(),
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
            'notes' => 'Two email subscriptions at GoDaddy',
        ]);

        // AWS Hosting (70 AED monthly)
        $upsert([
            'name' => 'AWS Hosting',
            'vendor' => 'Amazon Web Services',
            'unit_amount' => 70,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_MONTHLY,
            'repeats_every' => 1,
            'active_months_mask' => 0,
            'start_date' => now()->startOfMonth(),
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
            'notes' => 'AWS Billing period: November 1 - November 30, 2025',
        ]);

        // License Renewal (1800 each month for 6 months, yearly cycle)
        // Default to Jan-Jun; adjust months as needed
        $maskJanToJun = 0;
        for ($i = 1; $i <= 6; $i++) { $maskJanToJun |= (1 << ($i-1)); }
        $upsert([
            'name' => 'Trade License Renewal Installments',
            'vendor' => 'Dubai DED / Government',
            'unit_amount' => 1800,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_MONTHLY,
            'repeats_every' => 1,
            'active_months_mask' => $maskJanToJun,
            'start_date' => Carbon::create(now()->year, 1, 1),
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => true,
            'notes' => '6-month yearly cycle; adjust active months to match actual payment schedule',
        ]);

        // Ejari Contract (2500 AED yearly)
        $upsert([
            'name' => 'Ejari Contract',
            'vendor' => 'Ejari',
            'unit_amount' => 2500,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_YEARLY,
            'repeats_every' => 1,
            'active_months_mask' => 0,
            'start_date' => now()->startOfMonth(), // set to actual renewal month
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
            'notes' => 'Set start_date to the actual renewal month',
        ]);

        // Bank Charges (200 AED monthly)
        $upsert([
            'name' => 'Bank Charges',
            'vendor' => 'Bank',
            'unit_amount' => 200,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_MONTHLY,
            'repeats_every' => 1,
            'active_months_mask' => 0,
            'start_date' => now()->startOfMonth(),
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
        ]);

        // Cursor (70 AED monthly)
        $upsert([
            'name' => 'Cursor Subscription',
            'vendor' => 'Cursor',
            'unit_amount' => 70,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_MONTHLY,
            'repeats_every' => 1,
            'active_months_mask' => 0,
            'start_date' => now()->startOfMonth(),
            'next_due_date' => null,
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
        ]);

        // Ultimate Domain Protection (55 AED yearly)
        // Domain: maxmedme.com
        // Renews: July 30, 2026
        $upsert([
            'name' => 'Ultimate Domain Protection',
            'vendor' => 'GoDaddy',
            'unit_amount' => 55,
            'quantity' => 1,
            'currency' => 'AED',
            'frequency' => RecurringExpense::FREQUENCY_YEARLY,
            'repeats_every' => 1,
            'active_months_mask' => 0,
            'start_date' => Carbon::create(2026, 7, 1), // July 2026
            'next_due_date' => Carbon::create(2026, 7, 30), // Renews July 30, 2026
            'status' => RecurringExpense::STATUS_ACTIVE,
            'is_installment' => false,
            'notes' => 'Domain: maxmedme.com - Renews July 30, 2026',
        ]);
    }
}


