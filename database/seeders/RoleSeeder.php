<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role - Update if exists, create if not
        Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full administrative access to all system features',
                'permissions' => [
                    'dashboard.view',
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'products.view', 'products.create', 'products.edit', 'products.delete',
                    'orders.view', 'orders.create', 'orders.edit', 'orders.delete',
                    'customers.view', 'customers.create', 'customers.edit', 'customers.delete',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit', 'deliveries.delete',
                    'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
                    'brands.view', 'brands.create', 'brands.edit', 'brands.delete',
                    'news.view', 'news.create', 'news.edit', 'news.delete',
                    'supplier.products.view', 'supplier.products.create', 'supplier.products.edit', 'supplier.products.delete'
                ],
                'is_active' => true,
            ]
        );

        // Manager Role
        Role::updateOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can manage products, orders, and customer relations',
                'permissions' => [
                    'dashboard.view',
                    'products.view', 'products.create', 'products.edit',
                    'orders.view', 'orders.create', 'orders.edit',
                    'customers.view', 'customers.create', 'customers.edit',
                    'deliveries.view', 'deliveries.create', 'deliveries.edit',
                    'categories.view', 'categories.create', 'categories.edit',
                    'brands.view', 'brands.create', 'brands.edit',
                    'news.view', 'news.create', 'news.edit',
                ],
                'is_active' => true,
            ]
        );

        // Sales Role
        Role::updateOrCreate(
            ['name' => 'sales'],
            [
                'display_name' => 'Sales Representative',
                'description' => 'Can manage orders and customer relations',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view', 'orders.create',
                    'customers.view', 'customers.create',
                    'deliveries.view',
                ],
                'is_active' => true,
            ]
        );

        // Support Role
        Role::updateOrCreate(
            ['name' => 'support'],
            [
                'display_name' => 'Support Agent',
                'description' => 'Can view orders and assist customers',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'customers.view',
                    'deliveries.view',
                ],
                'is_active' => true,
            ]
        );

        // Supplier Role
        Role::updateOrCreate(
            ['name' => 'supplier'],
            [
                'display_name' => 'Supplier',
                'description' => 'Can manage their own products with limited access',
                'permissions' => [
                    'supplier.products.view',
                    'supplier.products.create', 
                    'supplier.products.edit',
                    'supplier.products.delete'
                ],
                'is_active' => true,
            ]
        );

        // Viewer Role
        Role::updateOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Read-only access to most system information',
                'permissions' => [
                    'dashboard.view',
                    'products.view',
                    'orders.view',
                    'customers.view',
                    'deliveries.view',
                    'categories.view',
                    'brands.view',
                    'news.view',
                ],
                'is_active' => true,
            ]
        );
    }
} 