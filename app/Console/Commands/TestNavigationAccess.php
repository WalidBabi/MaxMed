<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class TestNavigationAccess extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:navigation {email}';

    /**
     * The console command description.
     */
    protected $description = 'Test navigation access for a user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User with email {$email} not found!");
            return 1;
        }
        
        $this->info("🧪 NAVIGATION ACCESS TEST");
        $this->line("==============================");
        $this->line("👤 User: {$user->name} ({$user->email})");
        $this->line("🎭 Role: " . ($user->role ? $user->role->display_name : 'No Role'));
        
        $this->newLine();
        $this->info("🔍 Permission Checks:");
        
        // Check individual permissions
        $hasDashboardView = $user->hasPermission('dashboard.view');
        $hasCrmAccess = $user->hasPermission('crm.access');
        $hasSupplierView = $user->hasPermission('supplier.products.view');
        $isAdmin = $user->isAdmin();
        
        $this->line("   dashboard.view: " . ($hasDashboardView ? '✅ YES' : '❌ NO'));
        $this->line("   crm.access: " . ($hasCrmAccess ? '✅ YES' : '❌ NO'));
        $this->line("   supplier.products.view: " . ($hasSupplierView ? '✅ YES' : '❌ NO'));
        $this->line("   isAdmin(): " . ($isAdmin ? '✅ YES' : '❌ NO'));
        
        $this->newLine();
        $this->info("🎯 Navigation Visibility:");
        
        // Test navigation logic (same as in blade templates)
        $showAdminPortal = $hasDashboardView || $isAdmin;
        $showCrmDashboard = $hasCrmAccess || $isAdmin;
        $showSupplierDashboard = $hasSupplierView;
        
        $this->line("   Admin Portal: " . ($showAdminPortal ? '✅ VISIBLE' : '❌ HIDDEN'));
        $this->line("   CRM Dashboard: " . ($showCrmDashboard ? '✅ VISIBLE' : '❌ HIDDEN'));
        $this->line("   Supplier Dashboard: " . ($showSupplierDashboard ? '✅ VISIBLE' : '❌ HIDDEN'));
        
        $this->newLine();
        
        if ($showAdminPortal && $showCrmDashboard) {
            $this->info("🎉 SUCCESS! User should see both Admin Portal and CRM Dashboard in navigation!");
        } elseif ($showAdminPortal) {
            $this->warn("⚠️  User can see Admin Portal but NOT CRM Dashboard");
        } elseif ($showCrmDashboard) {
            $this->warn("⚠️  User can see CRM Dashboard but NOT Admin Portal");
        } else {
            $this->error("❌ User cannot see Admin Portal OR CRM Dashboard");
        }
        
        $this->newLine();
        $this->info("📋 Navigation Template Logic:");
        $this->line("   Admin Portal shows when: hasPermission('dashboard.view') OR isAdmin()");
        $this->line("   CRM Dashboard shows when: hasPermission('crm.access') OR isAdmin()");
        $this->line("   Supplier Dashboard shows when: hasPermission('supplier.products.view')");
        
        return 0;
    }
}
