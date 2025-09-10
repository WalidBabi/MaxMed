<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

echo "=== Fixing Lead Permissions ===\n\n";

// Find the user and their role
$user = User::where('email', 'walid.babi.dubai@gmail.com')->first();
if (!$user) {
    echo "❌ User not found\n";
    exit;
}

echo "👤 User: {$user->name} ({$user->email})\n";
echo "Role: " . ($user->role ? $user->role->name : 'No role') . "\n";

if (!$user->role) {
    echo "❌ User has no role assigned\n";
    exit;
}

$role = $user->role;
echo "\n--- Current Role Permissions ---\n";

// Get current permissions
$currentPermissions = $role->permissions;
foreach ($currentPermissions as $permission) {
    echo "✅ {$permission->name}\n";
}

// Remove the crm.leads.edit permission
$editPermission = Permission::where('name', 'crm.leads.edit')->first();
if ($editPermission) {
    $role->permissions()->detach($editPermission->id);
    echo "\n✅ Removed crm.leads.edit permission from role\n";
} else {
    echo "\n❌ crm.leads.edit permission not found\n";
}

// Refresh the role to get updated permissions
$role->refresh();
$updatedPermissions = $role->permissions;

echo "\n--- Updated Role Permissions ---\n";
foreach ($updatedPermissions as $permission) {
    echo "✅ {$permission->name}\n";
}

echo "\n--- Verification ---\n";
// Simulate authentication and test
auth()->login($user);
echo "User can edit leads: " . ($user->hasPermission('crm.leads.edit') ? '❌ YES (BAD)' : '✅ NO (GOOD)') . "\n";
echo "User can create leads: " . ($user->hasPermission('crm.leads.create') ? '✅ YES (GOOD)' : '❌ NO (BAD)') . "\n";
echo "User can view leads: " . ($user->hasPermission('crm.leads.view') ? '✅ YES (GOOD)' : '❌ NO (BAD)') . "\n";

auth()->logout();

echo "\n✅ Lead edit permission removed successfully!\n";
