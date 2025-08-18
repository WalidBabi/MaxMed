<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CrmLead;
use Illuminate\Support\Facades\Storage;

class CleanupMissingAttachments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachments:cleanup-missing {--dry-run : Show what would be cleaned without actually removing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up missing attachment files from CRM leads';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🧹 Starting cleanup of missing attachments...');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $leads = CrmLead::whereNotNull('attachments')->get();
        $totalLeads = $leads->count();
        $totalCleaned = 0;
        $totalFilesRemoved = 0;
        
        $this->info("📋 Found {$totalLeads} leads with attachments");
        
        foreach ($leads as $lead) {
            $attachments = $lead->attachments;
            
            if (!$attachments || !is_array($attachments)) {
                continue;
            }
            
            $validAttachments = [];
            $removedFiles = [];
            
            foreach ($attachments as $attachment) {
                if (!isset($attachment['path'])) {
                    $removedFiles[] = $attachment['original_name'] ?? 'Unknown file';
                    continue;
                }
                
                if (Storage::disk('public')->exists($attachment['path'])) {
                    $validAttachments[] = $attachment;
                } else {
                    $removedFiles[] = $attachment['original_name'] ?? 'Unknown file';
                }
            }
            
            if (!empty($removedFiles)) {
                $this->warn("🔴 Lead #{$lead->id} ({$lead->full_name}) - Missing files:");
                foreach ($removedFiles as $fileName) {
                    $this->line("   - {$fileName}");
                }
                
                if (!$dryRun) {
                    $lead->attachments = $validAttachments;
                    $lead->save();
                    
                    $lead->logActivity(
                        'note', 
                        'Attachments cleaned', 
                        count($removedFiles) . ' missing file(s) removed from database'
                    );
                }
                
                $totalCleaned++;
                $totalFilesRemoved += count($removedFiles);
            }
        }
        
        if ($totalCleaned > 0) {
            $this->info("✅ Cleanup completed!");
            $this->line("📊 Summary:");
            $this->line("   - Leads cleaned: {$totalCleaned}");
            $this->line("   - Missing files removed: {$totalFilesRemoved}");
            
            if ($dryRun) {
                $this->warn("⚠️  This was a dry run. Run without --dry-run to apply changes.");
            }
        } else {
            $this->info("✨ No missing attachments found. All files are present!");
        }
        
        return 0;
    }
}