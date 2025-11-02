<?php

namespace App\Console\Commands;

use App\Models\RecurringExpense;
use App\Models\User;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotifyDueExpenses extends Command
{
    protected $signature = 'expenses:notify-due';

    protected $description = 'Send web push notifications to superadmin users for expenses due or expiring soon';

    public function handle(PushNotificationService $push): int
    {
        $now = Carbon::now();

        $dueWithinDays = (int) config('expenses.notify.due_within_days', 3);
        $expireWithinDays = (int) config('expenses.notify.expire_within_days', 7);

        $startOfToday = $now->copy()->startOfDay();
        $dueWindowEnd = $now->copy()->addDays($dueWithinDays)->startOfDay();
        $expireWindowEnd = $now->copy()->addDays($expireWithinDays)->endOfDay();

        // Find due soon expenses (based on next_due_date)
        $dueSoon = RecurringExpense::query()
            ->where('status', RecurringExpense::STATUS_ACTIVE)
            ->whereNotNull('next_due_date')
            // Include overdue and due-within-window
            ->whereDate('next_due_date', '<=', $dueWindowEnd->toDateString())
            ->orderBy('next_due_date')
            ->get(['id', 'name', 'vendor', 'next_due_date', 'is_installment']);

        // Find expiring soon expenses (based on end_date)
        $expiringSoon = RecurringExpense::query()
            ->where('status', RecurringExpense::STATUS_ACTIVE)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$now->copy()->startOfDay()->toDateString(), $expireWindowEnd->toDateString()])
            ->orderBy('end_date')
            ->get(['id', 'name', 'vendor', 'end_date']);

        if ($dueSoon->isEmpty() && $expiringSoon->isEmpty()) {
            $this->info('No due or expiring expenses within the configured windows.');
            return 0;
        }

        // Prepare summary
        $overdueCount = $dueSoon->filter(function ($e) use ($startOfToday) {
            return \Carbon\Carbon::parse($e->next_due_date)->lt($startOfToday);
        })->count();
        $installmentsDueCount = $dueSoon->where('is_installment', true)->count();
        $expensesDueCount = $dueSoon->count() - $installmentsDueCount;
        $expiringCount = $expiringSoon->count();

        $title = 'Expenses require attention';

        $lines = [];
        if ($expensesDueCount > 0) {
            $lines[] = $expensesDueCount.' expense'.($expensesDueCount === 1 ? '' : 's').' due or overdue within '.$dueWithinDays.' day'.($dueWithinDays === 1 ? '' : 's');
        }
        if ($installmentsDueCount > 0) {
            $lines[] = $installmentsDueCount.' installment'.($installmentsDueCount === 1 ? '' : 's').' due or overdue within '.$dueWithinDays.' day'.($dueWithinDays === 1 ? '' : 's');
        }
        if ($overdueCount > 0) {
            $lines[] = $overdueCount.' item'.($overdueCount === 1 ? '' : 's').' already overdue';
        }
        if ($expiringCount > 0) {
            $lines[] = $expiringCount.' expense'.($expiringCount === 1 ? '' : 's').' expiring within '.$expireWithinDays.' day'.($expireWithinDays === 1 ? '' : 's');
        }

        // Include up to 5 specific items for quick context
        /** @var Collection<int, string> $detailLines */
        $detailLines = collect();
        $dueSoon->take(3)->each(function ($e) use ($detailLines, $startOfToday) {
            $label = (\Carbon\Carbon::parse($e->next_due_date)->lt($startOfToday) ? '[Overdue] ' : '');
            $detailLines->push($label.($e->is_installment ? '[Installment] ' : '').($e->name).(
                $e->vendor ? ' — '.$e->vendor : ''
            ).' • Due '.Carbon::parse($e->next_due_date)->toFormattedDateString());
        });
        $expiringSoon->take(max(0, 5 - $detailLines->count()))->each(function ($e) use ($detailLines) {
            $detailLines->push(($e->name).(
                $e->vendor ? ' — '.$e->vendor : ''
            ).' • Expires '.Carbon::parse($e->end_date)->toFormattedDateString());
        });

        $body = implode("\n", array_merge($lines, $detailLines->all()));

        $url = url('/admin/business-expenses/forecast');

        // Superadmin recipients
        $superAdmins = User::query()
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['super_admin', 'superadmin', 'super-administrator']);
            })
            ->orWhereHas('role', function ($q) {
                $q->whereIn('name', ['super_admin', 'superadmin', 'super-administrator']);
            })
            ->get(['id']);

        if ($superAdmins->isEmpty()) {
            $this->warn('No superadmin users found to notify.');
            return 0;
        }

        $totalSent = 0;
        foreach ($superAdmins as $user) {
            try {
                $sent = $push->sendToUser((int) $user->id, $title, $body, $url);
                $totalSent += $sent;
            } catch (\Throwable $e) {
                Log::warning('Failed sending expense due notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Expense notifications dispatched. Push messages sent: '.$totalSent);
        return 0;
    }
}


