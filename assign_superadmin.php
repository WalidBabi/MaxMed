<?php
/**
 * Production Script: Assign Superadmin Role to User
 * 
 * This script assigns the super_admin role with all permissions to a specific user.
 * 
 * Usage: php assign_superadmin.php
 * 
 * Safety Features:
 * - Validates user exists
 * - Validates super_admin role exists
 * - Creates missing permissions if needed
 * - Provides detailed logging
 * - Can be run multiple times safely
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuperadminAssigner
{
    private $targetEmail = 'wbabi@localhost.com';
    private $superAdminRoleName = 'super_admin';
    
    public function run()
    {
        echo "=== SUPERADMIN ROLE ASSIGNMENT SCRIPT ===\n";
        echo "Target User: {$this->targetEmail}\n";
        echo "Target Role: {$this->superAdminRoleName}\n";
        echo "Timestamp: " . now() . "\n\n";
        
        try {
            DB::beginTransaction();
            
            // Step 1: Find or create the user
            $user = $this->findOrCreateUser();
            
            // Step 2: Find or create the super_admin role
            $superAdminRole = $this->findOrCreateSuperAdminRole();
            
            // Step 3: Create all necessary permissions
            $this->createAllPermissions();
            
            // Step 4: Assign role to user
            $this->assignRoleToUser($user, $superAdminRole);
            
            // Step 5: Verify the assignment
            $this->verifyAssignment($user);
            
            DB::commit();
            
            echo "\n✅ SUCCESS: Superadmin role assigned successfully!\n";
            echo "User {$this->targetEmail} now has super_admin role with all permissions.\n";
            
            // Log the action
            Log::info("Superadmin role assigned to user {$this->targetEmail}", [
                'user_id' => $user->id,
                'role_id' => $superAdminRole->id,
                'assigned_by' => 'system_script',
                'timestamp' => now()
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            echo "\n❌ ERROR: " . $e->getMessage() . "\n";
            echo "Transaction rolled back. No changes made.\n";
            
            Log::error("Failed to assign superadmin role to user {$this->targetEmail}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            exit(1);
        }
    }
    
    private function findOrCreateUser()
    {
        echo "Step 1: Finding user...\n";
        
        $user = User::where('email', $this->targetEmail)->first();
        
        if (!$user) {
            echo "❌ User {$this->targetEmail} not found!\n";
            echo "Please create the user first or check the email address.\n";
            throw new \Exception("User not found: {$this->targetEmail}");
        }
        
        echo "✅ Found user: {$user->name} (ID: {$user->id})\n";
        return $user;
    }
    
    private function findOrCreateSuperAdminRole()
    {
        echo "\nStep 2: Finding super_admin role...\n";
        
        $role = Role::where('name', $this->superAdminRoleName)->first();
        
        if (!$role) {
            echo "Creating super_admin role...\n";
            $role = Role::create([
                'name' => $this->superAdminRoleName,
                'display_name' => 'Super Administrator',
                'description' => 'Full system access with all permissions'
            ]);
            echo "✅ Created super_admin role (ID: {$role->id})\n";
        } else {
            echo "✅ Found super_admin role (ID: {$role->id})\n";
        }
        
        return $role;
    }
    
    private function createAllPermissions()
    {
        echo "\nStep 3: Creating all necessary permissions...\n";
        
        // Define all permissions that should exist in the system
        $allPermissions = [
            // Dashboard
            'dashboard.view',
            
            // Sales Management
            'quotations.view', 'quotations.create', 'quotations.edit', 'quotations.delete', 'quotations.approve', 'quotations.send', 'quotations.compare', 'quotations.convert',
            'invoices.view', 'invoices.create', 'invoices.edit', 'invoices.delete', 'invoices.send', 'invoices.manage_payments',
            'orders.view', 'orders.view_all', 'orders.view_own', 'orders.create', 'orders.edit', 'orders.delete', 'orders.manage_status', 'orders.process', 'orders.export',
            'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete', 'deliveries.track', 'deliveries.confirm',
            'cash_receipts.view', 'cash_receipts.create', 'cash_receipts.edit', 'cash_receipts.delete',
            'sales_targets.view', 'sales_targets.create', 'sales_targets.edit', 'sales_targets.delete',
            
            // Suppliers Management
            'suppliers.view', 'suppliers.create', 'suppliers.edit', 'suppliers.delete', 'suppliers.view_performance',
            'purchase_orders.view', 'purchase_orders.create', 'purchase_orders.edit', 'purchase_orders.delete', 'purchase_orders.approve', 'purchase_orders.send', 'purchase_orders.manage_status', 'purchase_orders.view_financials',
            'inquiries.view', 'inquiries.create', 'inquiries.edit', 'inquiries.delete', 'inquiries.broadcast', 'inquiries.forward',
            
            // Product Catalog
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
            
            // User Management
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            
            // Content Management
            'news.view', 'news.create', 'news.edit', 'news.delete',
            'feedback.view', 'feedback.create',
            
            // Analytics
            'analytics.view', 'user_behavior.view',
            
            // CRM System
            'crm.access', 'crm.leads.view', 'crm.leads.create', 'crm.leads.edit', 'crm.leads.delete',
            'crm.contacts.view', 'crm.contacts.create', 'crm.contacts.edit',
            'crm.contact-submissions.view', 'crm.quotation-requests.view',
            
            // System
            'system.admin', 'system.maintenance', 'system.backup', 'system.logs'
        ];
        
        $createdCount = 0;
        $existingCount = 0;
        
        foreach ($allPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                [
                    'display_name' => $this->formatDisplayName($permissionName),
                    'description' => "Permission to {$permissionName}"
                ]
            );
            
            if ($permission->wasRecentlyCreated) {
                $createdCount++;
            } else {
                $existingCount++;
            }
        }
        
        echo "✅ Created {$createdCount} new permissions\n";
        echo "✅ Found {$existingCount} existing permissions\n";
        echo "✅ Total permissions: " . Permission::count() . "\n";
    }
    
    private function assignRoleToUser($user, $role)
    {
        echo "\nStep 4: Assigning role to user...\n";
        
        if ($user->role_id == $role->id) {
            echo "✅ User already has super_admin role\n";
            return;
        }
        
        $oldRole = $user->role ? $user->role->name : 'No role';
        $user->update(['role_id' => $role->id]);
        
        echo "✅ Assigned super_admin role to user\n";
        echo "   Previous role: {$oldRole}\n";
        echo "   New role: {$role->name}\n";
    }
    
    private function verifyAssignment($user)
    {
        echo "\nStep 5: Verifying assignment...\n";
        
        $user->refresh();
        
        if (!$user->role) {
            throw new \Exception("User has no role assigned");
        }
        
        if ($user->role->name !== $this->superAdminRoleName) {
            throw new \Exception("User role is not super_admin: {$user->role->name}");
        }
        
        echo "✅ User role verified: {$user->role->name}\n";
        echo "✅ User ID: {$user->id}\n";
        echo "✅ Role ID: {$user->role->id}\n";
        
        // Test FeatureAccessService
        $featureService = new \App\Services\FeatureAccessService();
        $testFeatures = ['dashboard.view', 'cash_receipts.index', 'sales_targets.index', 'users.view'];
        
        echo "\nTesting feature access:\n";
        foreach ($testFeatures as $feature) {
            $hasAccess = $featureService->canAccess($user, $feature);
            echo "   {$feature}: " . ($hasAccess ? "✅ YES" : "❌ NO") . "\n";
        }
    }
    
    private function formatDisplayName($permissionName)
    {
        return ucwords(str_replace(['.', '_'], [' ', ' '], $permissionName));
    }
}

// Run the script
$assigner = new SuperadminAssigner();
$assigner->run();

echo "\n=== SCRIPT COMPLETED ===\n";
echo "The user wbabi@localhost.com now has superadmin access.\n";
echo "You can now access all features in the admin panel.\n";
