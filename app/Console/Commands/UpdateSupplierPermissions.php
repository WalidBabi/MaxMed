<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class UpdateSupplierPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:update-supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update supplier role with new category-specific permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating supplier permissions...');

        // Find the supplier role
        $supplierRole = Role::where('name', 'supplier')->first();

        if (!$supplierRole) {
            $this->error('Supplier role not found!');
            return 1;
        }

        // Define new supplier permissions
        $supplierPermissions = [
            'supplier.products.view',
            'supplier.products.create',
            'supplier.products.edit',
            'supplier.products.delete',
            'supplier.products.specifications',
            'supplier.categories.view',
            'supplier.categories.products',
            'supplier.orders.view',
            'supplier.orders.manage',
            'supplier.feedback.create',
            'supplier.inquiries.view',
            'supplier.inquiries.respond',
        ];

        // Update the supplier role permissions
        $supplierRole->update([
            'permissions' => $supplierPermissions,
            'description' => 'Can manage products within assigned categories only'
        ]);

        $this->info('✅ Supplier role updated with category-specific permissions');
        $this->table(
            ['Permission', 'Description'],
            array_map(function($permission) {
                return [$permission, $this->getPermissionDescription($permission)];
            }, $supplierPermissions)
        );

        // Show current supplier assignments
        $suppliers = \App\Models\User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->with('activeAssignedCategories')->get();

        if ($suppliers->count() > 0) {
            $this->info("\n📊 Current Supplier Category Assignments:");
            foreach ($suppliers as $supplier) {
                $categories = $supplier->activeAssignedCategories->pluck('name')->join(', ') ?: 'No categories assigned';
                $this->line("• {$supplier->name}: {$categories}");
            }
        } else {
            $this->warn('No suppliers found in the system');
        }

        $this->info("\n🔧 Next Steps:");
        $this->line("1. Assign categories to suppliers via: Admin → Supplier Categories");
        $this->line("2. Suppliers will only see products/forms for their assigned categories");
        $this->line("3. Test the permissions by logging in as a supplier");

        return 0;
    }

    /**
     * Get human-readable description for permission
     */
    private function getPermissionDescription($permission)
    {
        $descriptions = [
            'supplier.products.view' => 'View own products',
            'supplier.products.create' => 'Create products in assigned categories',
            'supplier.products.edit' => 'Edit own products',
            'supplier.products.delete' => 'Delete own products',
            'supplier.products.specifications' => 'Manage product specifications',
            'supplier.categories.view' => 'View assigned categories',
            'supplier.categories.products' => 'Manage products in assigned categories',
            'supplier.orders.view' => 'View own orders',
            'supplier.orders.manage' => 'Manage own orders',
            'supplier.feedback.create' => 'Submit feedback',
            'supplier.inquiries.view' => 'View assigned inquiries',
            'supplier.inquiries.respond' => 'Respond to inquiries',
        ];

        return $descriptions[$permission] ?? 'No description';
    }
}
