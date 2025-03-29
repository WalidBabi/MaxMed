<?php

namespace App\Console\Commands;

use App\Models\ProductReservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredReservations extends Command
{
    protected $signature = 'reservations:cleanup';
    protected $description = 'Clean up expired product reservations';

    public function handle()
    {
        try {
            // Get count before cleanup
            $expiredCount = ProductReservation::where('status', 'pending')
                ->where('expires_at', '<', now())
                ->count();

            // Perform the cleanup
            $updated = ProductReservation::where('status', 'pending')
                ->where('expires_at', '<', now())
                ->update(['status' => 'cancelled']);

            // Log the results
            Log::info('Reservation cleanup completed', [
                'expired_found' => $expiredCount,
                'records_updated' => $updated,
                'current_time' => now()->toDateTimeString()
            ]);

            $this->info("Found {$expiredCount} expired reservations.");
            $this->info("Updated {$updated} reservations to cancelled.");
        } catch (\Exception $e) {
            Log::error('Error in reservation cleanup: ' . $e->getMessage());
            $this->error('Failed to cleanup reservations: ' . $e->getMessage());
        }
    }
} 