<?php

return [
    'notify' => [
        // Number of days ahead to consider an expense or installment as "due soon"
        'due_within_days' => (int) env('EXPENSES_DUE_WITHIN_DAYS', 3),

        // Number of days ahead to consider an expense as "expiring soon" (based on end_date)
        'expire_within_days' => (int) env('EXPENSES_EXPIRE_WITHIN_DAYS', 7),

        // Local hour of day to run notifications (24h format); scheduler will use this
        'send_hour_local' => (int) env('EXPENSES_NOTIFY_HOUR', 9),
    ],
];


