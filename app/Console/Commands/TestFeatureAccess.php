<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use App\Services\FeatureAccessService;
use Illuminate\Console\Command;

class TestFeatureAccess extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:feature-access {email?}';

    /**
     * The console command description.
     */
    protected $description = 'Test feature access for users based on their roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        
        if ($email) {
            return $this->testSpecificUser($email);
        }
        
        return $this->testAllRoles();
    }
    
    /**
     * Test feature access for a specific user
     */
    private function testSpecificUser(string $email): int
    {
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ User with email {$email} not found!");
            return 1;
        }
        
        $this->info("🧪 FEATURE ACCESS TEST FOR USER");
        $this->line("=====================================");
        $this->line("👤 User: {$user->name} ({$user->email})");
        $this->line("🎭 Role: " . ($user->role ? $user->role->display_name : 'No Role'));
        
        $this->newLine();
        $this->info("🔍 Testing AdminMiddleware Access:");
        
        $hasAdminAccess = $user->isAdmin() || 
                         $user->hasPermission('dashboard.view') || 
                         $user->hasAnyRole(['super_admin', 'system_admin', 'business_admin', 'operations_manager', 'purchasing_crm_assistant']);
        
        $this->line("   Admin Area Access: " . ($hasAdminAccess ? '✅ ALLOWED' : '❌ BLOCKED'));
        
        $this->newLine();
        $this->info("🎯 Feature Access Summary:");
        
        $accessibleFeatures = FeatureAccessService::getAccessibleFeatures($user);
        $featureCategories = FeatureAccessService::getFeatureCategories();
        
        $totalFeatures = 0;
        $accessibleCount = 0;
        
        foreach ($featureCategories as $category => $features) {
            $categoryAccessible = [];
            foreach ($features as $feature) {
                $totalFeatures++;
                if (in_array($feature, $accessibleFeatures)) {
                    $categoryAccessible[] = $feature;
                    $accessibleCount++;
                }
            }
            
            if (!empty($categoryAccessible)) {
                $this->line("   📁 {$category}: " . count($categoryAccessible) . "/" . count($features) . " features");
                foreach ($categoryAccessible as $feature) {
                    $this->line("      ✅ {$feature}");
                }
            } else {
                $this->line("   📁 {$category}: ❌ No access");
            }
        }
        
        $this->newLine();
        $this->info("📊 Access Summary: {$accessibleCount}/{$totalFeatures} features accessible");
        
        if ($hasAdminAccess && $accessibleCount > 0) {
            $this->info("🎉 User should be able to access the admin portal!");
        } elseif (!$hasAdminAccess) {
            $this->error("❌ User will get 403 error when accessing admin area");
        }
        
        return 0;
    }
    
    /**
     * Test feature access for all roles
     */
    private function testAllRoles(): int
    {
        $this->info("🧪 COMPREHENSIVE ROLE FEATURE ACCESS TEST");
        $this->line("==========================================");
        
        $roles = Role::with('permissions')->where('is_active', true)->get();
        
        if ($roles->isEmpty()) {
            $this->error("❌ No active roles found!");
            return 1;
        }
        
        foreach ($roles as $role) {
            $this->newLine();
            $this->info("🎭 Role: {$role->display_name}");
            $this->line("   Name: {$role->name}");
            $this->line("   Permissions: " . $role->permissions->count());
            
            // Create a mock user with this role to test access
            $mockUser = new User();
            $mockUser->role = $role;
            $mockUser->role_id = $role->id;
            
            // Test admin middleware access
            $hasAdminAccess = $mockUser->isAdmin() || 
                             $mockUser->hasPermission('dashboard.view') || 
                             $mockUser->hasAnyRole(['super_admin', 'system_admin', 'business_admin', 'operations_manager', 'purchasing_crm_assistant']);
            
            $this->line("   Admin Access: " . ($hasAdminAccess ? '✅ YES' : '❌ NO'));
            
            // Count accessible features
            $accessibleFeatures = FeatureAccessService::getAccessibleFeatures($mockUser);
            $this->line("   Accessible Features: " . count($accessibleFeatures));
            
            // Show key capabilities
            $keyFeatures = [
                'dashboard.view' => 'Dashboard',
                'crm.access' => 'CRM',
                'products.index' => 'Products',
                'orders.index' => 'Orders',
                'purchase_orders.index' => 'Purchase Orders',
                'suppliers.index' => 'Suppliers',
                'users.index' => 'User Management',
                'roles.index' => 'Role Management',
            ];
            
            $capabilities = [];
            foreach ($keyFeatures as $feature => $label) {
                if (FeatureAccessService::canAccess($mockUser, $feature)) {
                    $capabilities[] = $label;
                }
            }
            
            if (!empty($capabilities)) {
                $this->line("   Key Capabilities: " . implode(', ', $capabilities));
            } else {
                $this->line("   Key Capabilities: None");
            }
        }
        
        $this->newLine();
        $this->info("✅ Role feature access test completed!");
        $this->line("💡 Use 'php artisan test:feature-access user@example.com' to test a specific user");
        
        return 0;
    }
}
