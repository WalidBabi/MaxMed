<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\CrmLead;

echo "=== Testing Lead Permissions ===\n\n";

// Test the user with one-task-role-create-leads
$user = User::where('email', 'walid.babi.dubai@gmail.com')->first();
if ($user) {
    echo "👤 Testing user: {$user->name} ({$user->email})\n";
    echo "Role: " . ($user->role ? $user->role->name : 'No role') . "\n";
    
    // Simulate authentication
    auth()->login($user);
    
    echo "\n--- Current CRM Lead Permissions ---\n";
    $leadPermissions = [
        'crm.leads.view',
        'crm.leads.create',
        'crm.leads.edit',
        'crm.leads.delete',
        'crm.leads.assign',
        'crm.leads.convert',
    ];
    
    foreach ($leadPermissions as $permission) {
        $hasPermission = $user->hasPermission($permission);
        echo ($hasPermission ? '✅' : '❌') . " {$permission}\n";
    }
    
    echo "\n--- What User Should Be Able To Do ---\n";
    echo "✅ Create new leads (has crm.leads.create)\n";
    echo "✅ View leads (has crm.leads.view)\n";
    echo "❌ Edit leads (should NOT have crm.leads.edit)\n";
    echo "❌ Delete leads (should NOT have crm.leads.delete)\n";
    echo "❌ Assign leads (should NOT have crm.leads.assign)\n";
    echo "❌ Convert leads (should NOT have crm.leads.convert)\n";
    
    echo "\n--- What User Should NOT Be Able To Do ---\n";
    echo "❌ Edit leads they didn't create\n";
    echo "❌ Change lead status unless they're assigned to it\n";
    echo "❌ Assign leads to other users\n";
    echo "❌ Delete any leads\n";
    echo "❌ Convert leads to deals\n";
    
    // Check if there are any leads in the system
    $totalLeads = CrmLead::count();
    echo "\n--- Lead Data Check ---\n";
    echo "Total leads in system: {$totalLeads}\n";
    
    if ($totalLeads > 0) {
        $sampleLead = CrmLead::first();
        echo "Sample lead: {$sampleLead->full_name} (ID: {$sampleLead->id})\n";
        echo "Sample lead assigned to: " . ($sampleLead->assignedUser ? $sampleLead->assignedUser->name : 'No one') . "\n";
        echo "Sample lead created by: " . ($sampleLead->created_by ? $sampleLead->created_by : 'Unknown') . "\n";
    }
    
    auth()->logout();
} else {
    echo "❌ User not found\n";
}

echo "\n=== Issues Found ===\n";
echo "1. ❌ User has crm.leads.edit permission - should be removed\n";
echo "2. ❌ No check for lead assignment in edit/update methods\n";
echo "3. ❌ No check for lead ownership in status update methods\n";
echo "4. ❌ No permission checks in controller methods\n";
