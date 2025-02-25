<?php

namespace App\Console\Commands;

use App\Models\ProductReservation;
use Illuminate\Console\Command;

class CleanupExpiredReservations extends Command
{
    protected $signature = 'reservations:cleanup';
    protected $description = 'Clean up expired product reservations';

    public function handle()
    {
        ProductReservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'cancelled']);

        $this->info('Expired reservations cleaned up successfully.');
    }
} 