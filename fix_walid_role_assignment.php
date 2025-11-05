<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔧 Fixing Role Assignment for walid.babi.dubai@gmail.com\n";
echo "====================================================\n\n";

try {
    // Find the user
    $user = User::where('email', 'walid.babi.dubai@gmail.com')->first();
    
    if (!$user) {
        echo "❌ User not found with email: walid.babi.dubai@gmail.com\n";
        exit(1);
    }
    
    echo "✅ User found: {$user->name} ({$user->email})\n";
    echo "   Current role_id: " . ($user->role_id ?? 'NULL') . "\n\n";
    
    // Find the Purchasing & CRM Assistant role
    $role = Role::where('name', 'purchasing_crm_assistant')->first();
    
    if (!$role) {
        echo "❌ Role 'purchasing_crm_assistant' not found. Creating it...\n";
        
        // Create the role using the seeder
        $seeder = new \Database\Seeders\PurchasingCrmAssistantSeeder();
        $seeder->run();
        
        $role = Role::where('name', 'purchasing_crm_assistant')->first();
        
        if (!$role) {
            echo "❌ Failed to create role\n";
            exit(1);
        }
        
        echo "✅ Role created successfully\n";
    } else {
        echo "✅ Role found: {$role->display_name} (ID: {$role->id})\n";
    }
    
    echo "   Role permissions count: " . $role->permissions()->count() . "\n\n";
    
    // Assign the role to the user
    $user->role_id = $role->id;
    $user->save();
    
    echo "✅ Role assigned successfully!\n";
    echo "   User {$user->name} now has role: {$role->display_name}\n\n";
    
    // Test the permissions
    echo "🧪 Testing Permissions:\n";
    echo "   Can access quotations.index: " . (App\Services\FeatureAccessService::canAccess($user, 'quotations.index') ? 'YES ✅' : 'NO ❌') . "\n";
    echo "   Can access quotations.view: " . ($user->hasPermission('quotations.view') ? 'YES ✅' : 'NO ❌') . "\n";
    echo "   Can access quotations.create: " . ($user->hasPermission('quotations.create') ? 'YES ✅' : 'NO ❌') . "\n";
    echo "   Can access quotations.edit: " . ($user->hasPermission('quotations.edit') ? 'YES ✅' : 'NO ❌') . "\n";
    echo "   Can access quotations.compare: " . ($user->hasPermission('quotations.compare') ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    // Test sidebar access
    echo "🎯 Sidebar Access Test:\n";
    echo "   Can see Sales Management section: " . (App\Services\FeatureAccessService::canAccess($user, 'quotations.index') || App\Services\FeatureAccessService::canAccess($user, 'invoices.index') || App\Services\FeatureAccessService::canAccess($user, 'orders.index') ? 'YES ✅' : 'NO ❌') . "\n";
    echo "   Can see Purchasing & Procurement section: " . (App\Services\FeatureAccessService::canAccess($user, 'purchase_orders.index') || App\Services\FeatureAccessService::canAccess($user, 'suppliers.index') || App\Services\FeatureAccessService::canAccess($user, 'inquiries.index') ? 'YES ✅' : 'NO ❌') . "\n\n";
    
    echo "🎉 SUCCESS! The user should now be able to see quotations tabs in the sidebar.\n";
    echo "   Please have them refresh their browser and check the admin portal.\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}