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
        // Admin Role
        Role::create([
            'name' => 'admin',
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
            ],
            'is_active' => true,
        ]);

        // Manager Role
        Role::create([
            'name' => 'manager',
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
        ]);

        // Sales Representative Role
        Role::create([
            'name' => 'sales-rep',
            'display_name' => 'Sales Representative',
            'description' => 'Can view and manage orders and customers',
            'permissions' => [
                'dashboard.view',
                'orders.view', 'orders.create', 'orders.edit',
                'customers.view', 'customers.create', 'customers.edit',
                'deliveries.view',
                'products.view',
            ],
            'is_active' => true,
        ]);

        // Content Editor Role
        Role::create([
            'name' => 'content-editor',
            'display_name' => 'Content Editor',
            'description' => 'Can manage website content including news and product information',
            'permissions' => [
                'dashboard.view',
                'products.view', 'products.edit',
                'categories.view', 'categories.edit',
                'brands.view', 'brands.edit',
                'news.view', 'news.create', 'news.edit', 'news.delete',
            ],
            'is_active' => true,
        ]);

        // Viewer Role
        Role::create([
            'name' => 'viewer',
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
        ]);
    }
} 