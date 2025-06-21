<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Category;
use App\Models\SupplierCategory;

class AssignSupplierCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supplier:assign-categories {supplier_email} {category_names*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign categories to a supplier for testing permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $supplierEmail = $this->argument('supplier_email');
        $categoryNames = $this->argument('category_names');

        // Find supplier
        $supplier = User::where('email', $supplierEmail)->first();
        if (!$supplier) {
            $this->error("❌ Supplier with email '{$supplierEmail}' not found!");
            return 1;
        }

        if (!$supplier->isSupplier()) {
            $this->error("❌ User '{$supplierEmail}' is not a supplier!");
            return 1;
        }

        $this->info("👤 Found supplier: {$supplier->name}");

        // Find categories
        $categories = Category::whereIn('name', $categoryNames)->get();
        
        if ($categories->isEmpty()) {
            $this->error("❌ No valid categories found!");
            $this->info("Available categories:");
            Category::all()->each(function($cat) {
                $this->line("  • {$cat->name}");
            });
            return 1;
        }

        $this->info("📂 Found categories: " . $categories->pluck('name')->join(', '));

        // Assign categories
        foreach ($categories as $category) {
            SupplierCategory::updateOrCreate(
                [
                    'supplier_id' => $supplier->id,
                    'category_id' => $category->id,
                ],
                [
                    'status' => 'active',
                    'assigned_at' => now(),
                ]
            );
            $this->line("✅ Assigned: {$category->name}");
        }

        $this->info("\n🎉 Successfully assigned {$categories->count()} categories to {$supplier->name}");
        
        return 0;
    }
}
