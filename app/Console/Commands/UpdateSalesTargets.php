<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SalesTarget;

class UpdateSalesTargets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:update-targets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update achieved amounts for all active sales targets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sales targets update...');
        
        $targets = SalesTarget::active()->get();
        $updatedCount = 0;
        
        foreach ($targets as $target) {
            $oldAchieved = $target->achieved_amount;
            $target->updateAchievedAmount();
            $target->refresh();
            
            if ($oldAchieved != $target->achieved_amount) {
                $updatedCount++;
                $this->line("Updated target '{$target->name}': {$oldAchieved} → {$target->achieved_amount}");
            }
        }
        
        $this->info("Completed! Updated {$updatedCount} out of {$targets->count()} active targets.");
        
        return 0;
    }
} 