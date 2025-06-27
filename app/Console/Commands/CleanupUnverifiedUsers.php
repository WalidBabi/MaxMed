<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Customer;
use App\Models\SupplierInformation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-unverified {--days=7 : Number of days after which unverified users are deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove unverified users after specified number of days (default: 7 days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        
        $this->info("Cleaning up unverified users created before {$cutoffDate->format('Y-m-d H:i:s')}");
        
        // Find unverified users older than specified days
        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->with(['customer', 'supplierInformation'])
            ->get();
            
        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found to cleanup.');
            return 0;
        }
        
        $this->info("Found {$unverifiedUsers->count()} unverified users to cleanup:");
        
        $deletedCount = 0;
        
        foreach ($unverifiedUsers as $user) {
            DB::beginTransaction();
            
            try {
                $this->line("  - Deleting user: {$user->name} ({$user->email}) - Created: {$user->created_at->format('Y-m-d H:i:s')}");
                
                // Delete related records first
                if ($user->customer) {
                    $user->customer->delete();
                }
                
                if ($user->supplierInformation) {
                    $user->supplierInformation->delete();
                }
                
                // Delete any supplier categories
                $user->supplierCategories()->delete();
                
                // Delete the user
                $user->delete();
                
                DB::commit();
                $deletedCount++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  - Failed to delete user {$user->email}: " . $e->getMessage());
                Log::error("Failed to cleanup unverified user {$user->id}: " . $e->getMessage());
            }
        }
        
        $this->info("Successfully deleted {$deletedCount} unverified users.");
        Log::info("Cleanup completed: {$deletedCount} unverified users deleted after {$days} days.");
        
        return 0;
    }
}
