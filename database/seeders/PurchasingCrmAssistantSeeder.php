<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PurchasingCrmAssistantSeeder extends Seeder
{
    /**
     * Create a specialized role for purchasing and CRM assistance
     */
    public function run(): void
    {
        $this->command->info('👤 Creating Purchasing & CRM Assistant Role');
        
        // Create the new role
        $role = Role::updateOrCreate(
            ['name' => 'purchasing_crm_assistant'],
            [
                'display_name' => 'Purchasing & CRM Assistant',
                'description' => 'Assists with purchasing processes and manages own CRM leads (cannot edit other users\' leads)',
                'is_active' => true,
            ]
        );

        // Define specific permissions for this role
        $permissions = [
            // Dashboard Access
            'dashboard.view',
            
            // Product Management (View Only + Limited Editing)
            'products.view',
            'products.manage_inventory', // Can update stock levels
            'categories.view',
            'brands.view',
            
            // Purchasing & Procurement (Core Responsibilities)
            'purchase_orders.view',
            'purchase_orders.create',
            'purchase_orders.edit',
            'purchase_orders.send', // Can send POs to suppliers
            'purchase_orders.manage_status', // Can update PO status
            
            // Supplier Management (Limited)
            'suppliers.view',
            'suppliers.edit', // Can update supplier information
            'suppliers.view_performance',
            
            // Quotation Management (Purchasing Context)
            'quotations.view',
            'quotations.create',
            'quotations.edit',
            'quotations.compare', // Can compare supplier quotations
            
            // Order Management (Limited - Purchasing Related)
            'orders.view_all', // Can view orders to understand purchasing needs
            'orders.create', // Can create orders based on purchasing
            'orders.edit',
            
            // Customer Management (Basic - for CRM)
            'customers.view',
            'customers.create', // Can add new customers/leads
            'customers.edit',
            
            // CRM Access (LIMITED - Own Leads Only)
            'crm.access',
            'crm.leads.view', // Can view leads (but middleware will restrict to own)
            'crm.leads.view_requirements', // Can view lead requirements for purchasing context
            'crm.leads.create', // Can create new leads
            'crm.leads.edit', // Can edit own leads only
            'crm.leads.convert', // Can convert own leads
            
            // CRM Contacts (Limited)
            'crm.contacts.view',
            'crm.contacts.create',
            'crm.contacts.edit',
            
            // CRM Activities (Own Only)
            'crm.activities.view',
            'crm.activities.create',
            'crm.activities.edit',
            
            // CRM Tasks (Own Only)
            'crm.tasks.view',
            'crm.tasks.create',
            'crm.tasks.edit',
            
            // Invoice Management (View Only - for purchasing context)
            'invoices.view',
            
            // Delivery Tracking (for purchased items)
            'deliveries.view',
            'deliveries.track',
            
            // Feedback (Can provide feedback)
            'feedback.view',
            'supplier.feedback.create', // Can give feedback about suppliers
            
            // Analytics (Limited)
            'analytics.view', // Can view basic analytics
        ];

        // Assign permissions
        $permissionIds = [];
        $foundPermissions = 0;
        $missingPermissions = [];
        
        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permissionIds[] = $permission->id;
                $foundPermissions++;
            } else {
                $missingPermissions[] = $permissionName;
            }
        }
        
        $role->permissions()->sync($permissionIds);
        
        $this->command->info("✅ Created '{$role->display_name}' with {$foundPermissions} permissions");
        
        if (count($missingPermissions) > 0) {
            $this->command->warn("⚠️  Missing permissions: " . implode(', ', $missingPermissions));
        }
        
        // Display role summary
        $this->command->newLine();
        $this->command->info('📋 ROLE SUMMARY:');
        $this->command->line("   Name: {$role->name}");
        $this->command->line("   Display Name: {$role->display_name}");
        $this->command->line("   Total Permissions: {$foundPermissions}");
        $this->command->newLine();
        
        $this->command->info('🔑 KEY CAPABILITIES:');
        $this->command->line('   ✅ Full purchasing and procurement support');
        $this->command->line('   ✅ Can manage purchase orders and supplier relationships');
        $this->command->line('   ✅ Can create and edit own CRM leads');
        $this->command->line('   ✅ Can view and compare quotations');
        $this->command->line('   ✅ Can track deliveries and inventory');
        $this->command->line('   ❌ Cannot edit other users\' CRM leads');
        $this->command->line('   ❌ Cannot delete critical data');
        $this->command->line('   ❌ No user management or system admin access');
        
        $this->command->newLine();
        $this->command->info('🎯 This role is perfect for a purchasing assistant with CRM support duties!');
    }
}
