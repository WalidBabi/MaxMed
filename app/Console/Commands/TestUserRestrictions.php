<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FeatureAccessService;
use Illuminate\Console\Command;

class TestUserRestrictions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:user-restrictions {email}';

    /**
     * The console command description.
     */
    protected $description = 'Test that a user only sees features they have permissions for';

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
        
        $this->info("🔐 USER RESTRICTION TEST");
        $this->line("========================");
        $this->line("👤 User: {$user->name} ({$user->email})");
        $this->line("🎭 Role: " . ($user->role ? $user->role->display_name : 'No Role'));
        
        $this->newLine();
        $this->info("🧪 Testing Access Restrictions:");
        
        // Test admin area access
        $hasAdminAccess = $user->isAdmin() || 
                         $user->hasPermission('dashboard.view') || 
                         $user->hasAnyRole(['super_admin', 'system_admin', 'business_admin', 'operations_manager', 'purchasing_crm_assistant']);
        
        $this->line("   ✅ Can access admin area: " . ($hasAdminAccess ? 'YES' : 'NO'));
        
        // Test specific sensitive features
        $sensitiveFeatures = [
            'users.index' => 'User Management',
            'users.create' => 'Create Users',
            'users.edit' => 'Edit Users',
            'users.delete' => 'Delete Users',
            'roles.index' => 'Role Management',
            'roles.create' => 'Create Roles',
            'roles.edit' => 'Edit Roles',
            'roles.delete' => 'Delete Roles',
            'cash_receipts.index' => 'Cash Receipts',
            'sales_targets.index' => 'Sales Targets',
        ];
        
        $this->newLine();
        $this->info("🚫 Sensitive Features Test:");
        $blockedCount = 0;
        $allowedCount = 0;
        
        foreach ($sensitiveFeatures as $feature => $label) {
            $canAccess = FeatureAccessService::canAccess($user, $feature);
            if ($canAccess) {
                $this->line("   ✅ {$label}: ALLOWED");
                $allowedCount++;
            } else {
                $this->line("   ❌ {$label}: BLOCKED");
                $blockedCount++;
            }
        }
        
        // Test purchasing features (should be allowed for this role)
        $purchasingFeatures = [
            'purchase_orders.index' => 'Purchase Orders',
            'purchase_orders.create' => 'Create Purchase Orders',
            'suppliers.index' => 'Supplier Management',
            'quotations.index' => 'Quotations',
            'products.index' => 'Product Catalog',
        ];
        
        $this->newLine();
        $this->info("📦 Purchasing Features Test:");
        $purchasingAllowed = 0;
        
        foreach ($purchasingFeatures as $feature => $label) {
            $canAccess = FeatureAccessService::canAccess($user, $feature);
            if ($canAccess) {
                $this->line("   ✅ {$label}: ALLOWED");
                $purchasingAllowed++;
            } else {
                $this->line("   ❌ {$label}: BLOCKED");
            }
        }
        
        // Test CRM features (should be allowed for this role)
        $crmFeatures = [
            'crm.access' => 'CRM Access',
            'crm.leads.view' => 'View CRM Leads',
            'crm.leads.create' => 'Create CRM Leads',
            'crm.leads.edit' => 'Edit CRM Leads',
            'crm.contacts.view' => 'View CRM Contacts',
        ];
        
        $this->newLine();
        $this->info("👥 CRM Features Test:");
        $crmAllowed = 0;
        
        foreach ($crmFeatures as $feature => $label) {
            $canAccess = FeatureAccessService::canAccess($user, $feature);
            if ($canAccess) {
                $this->line("   ✅ {$label}: ALLOWED");
                $crmAllowed++;
            } else {
                $this->line("   ❌ {$label}: BLOCKED");
            }
        }
        
        $this->newLine();
        $this->info("📊 RESTRICTION SUMMARY:");
        $this->line("   Sensitive features blocked: {$blockedCount}/" . count($sensitiveFeatures));
        $this->line("   Purchasing features allowed: {$purchasingAllowed}/" . count($purchasingFeatures));
        $this->line("   CRM features allowed: {$crmAllowed}/" . count($crmFeatures));
        
        $this->newLine();
        
        // Expected results for Purchasing & CRM Assistant
        if ($user->role && $user->role->name === 'purchasing_crm_assistant') {
            $this->info("🎯 EXPECTED RESULTS FOR PURCHASING & CRM ASSISTANT:");
            $this->line("   ✅ Should have admin area access");
            $this->line("   ❌ Should NOT have user/role management");
            $this->line("   ❌ Should NOT have cash receipts access");
            $this->line("   ✅ Should have purchasing features");
            $this->line("   ✅ Should have CRM features");
            
            // Validate expectations
            $expectations = [
                'admin_access' => $hasAdminAccess,
                'no_user_management' => !FeatureAccessService::canAccess($user, 'users.index'),
                'no_role_management' => !FeatureAccessService::canAccess($user, 'roles.index'),
                'has_purchasing' => $purchasingAllowed >= 3,
                'has_crm' => $crmAllowed >= 3,
            ];
            
            $passed = array_filter($expectations);
            $total = count($expectations);
            
            $this->newLine();
            if (count($passed) === $total) {
                $this->info("🎉 ALL RESTRICTIONS WORKING CORRECTLY!");
            } else {
                $this->warn("⚠️  Some restrictions may not be working as expected");
            }
            
            $this->line("   Validation: " . count($passed) . "/{$total} checks passed");
        }
        
        return 0;
    }
}
