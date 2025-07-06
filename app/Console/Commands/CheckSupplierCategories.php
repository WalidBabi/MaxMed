<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\User;
use App\Models\SupplierCategory;

class CheckSupplierCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:supplier-categories {category_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check supplier category assignments and diagnose inquiry issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $categoryId = $this->argument('category_id');
        
        if ($categoryId) {
            $this->checkSpecificCategory($categoryId);
        } else {
            $this->checkAllCategories();
        }
        
        return 0;
    }
    
    private function checkSpecificCategory($categoryId)
    {
        $this->info("Checking category ID: {$categoryId}");
        
        // Check if category exists
        $category = Category::find($categoryId);
        if (!$category) {
            $this->error("Category ID {$categoryId} does not exist!");
            return;
        }
        
        $this->info("Category: {$category->name}");
        
        // Check supplier assignments
        $assignments = SupplierCategory::where('category_id', $categoryId)->get();
        $this->info("Total assignments: {$assignments->count()}");
        
        if ($assignments->isEmpty()) {
            $this->warn("No suppliers assigned to this category!");
            return;
        }
        
        $this->table(
            ['Supplier ID', 'Supplier Name', 'Status', 'Assigned At'],
            $assignments->map(function ($assignment) {
                return [
                    $assignment->supplier_id,
                    $assignment->supplier->name ?? 'Unknown',
                    $assignment->status,
                    $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i:s') : 'N/A'
                ];
            })
        );
        
        // Check active suppliers
        $activeAssignments = $assignments->where('status', 'active');
        $this->info("Active assignments: {$activeAssignments->count()}");
        
        if ($activeAssignments->isEmpty()) {
            $this->warn("No active suppliers for this category!");
        }
    }
    
    private function checkAllCategories()
    {
        $this->info("Checking all categories with supplier assignments...");
        
        $categories = Category::whereHas('suppliers')->with('suppliers')->get();
        
        if ($categories->isEmpty()) {
            $this->warn("No categories have supplier assignments!");
            return;
        }
        
        $this->table(
            ['Category ID', 'Category Name', 'Total Suppliers', 'Active Suppliers'],
            $categories->map(function ($category) {
                $totalSuppliers = $category->suppliers->count();
                $activeSuppliers = $category->suppliers->where('pivot.status', 'active')->count();
                
                return [
                    $category->id,
                    $category->name,
                    $totalSuppliers,
                    $activeSuppliers
                ];
            })
        );
    }
} 