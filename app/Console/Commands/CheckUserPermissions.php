<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'user:check-permissions {email}';

    /**
     * The console command description.
     */
    protected $description = 'Check permissions for a specific user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }
        
        $this->info("👤 User Details:");
        $this->line("   Name: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->line("   Role: " . ($user->role ? $user->role->display_name : 'No Role'));
        $this->line("   Role Name: " . ($user->role ? $user->role->name : 'No Role'));
        
        $this->newLine();
        $this->info("🔑 Navigation Permissions:");
        $this->line("   dashboard.view: " . ($user->hasPermission('dashboard.view') ? '✅ YES' : '❌ NO'));
        $this->line("   crm.access: " . ($user->hasPermission('crm.access') ? '✅ YES' : '❌ NO'));
        $this->line("   supplier.products.view: " . ($user->hasPermission('supplier.products.view') ? '✅ YES' : '❌ NO'));
        
        $this->newLine();
        $this->info("🎯 Navigation Access:");
        $this->line("   Admin Portal: " . (($user->hasPermission('dashboard.view') || $user->isAdmin()) ? '✅ VISIBLE' : '❌ HIDDEN'));
        $this->line("   CRM Dashboard: " . (($user->hasPermission('crm.access') || $user->isAdmin()) ? '✅ VISIBLE' : '❌ HIDDEN'));
        $this->line("   Supplier Dashboard: " . ($user->hasPermission('supplier.products.view') ? '✅ VISIBLE' : '❌ HIDDEN'));
        
        $this->newLine();
        $this->info("📊 Total Permissions: " . $user->getAllPermissions()->count());
        
        return 0;
    }
}
