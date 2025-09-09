<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignUserRoles extends Command
{
    protected $signature = 'roles:assign {user_id?} {role_name?}';
    protected $description = 'Assign roles to users';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleName = $this->argument('role_name');

        if ($userId && $roleName) {
            return $this->assignSpecificRole($userId, $roleName);
        }

        return $this->interactiveAssignment();
    }

    private function assignSpecificRole($userId, $roleName)
    {
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return 1;
        }

        $user->update(['role_id' => $role->id]);
        $this->info("✅ Assigned role '{$role->display_name}' to user '{$user->name}'");

        return 0;
    }

    private function interactiveAssignment()
    {
        $this->info('👥 User Role Assignment Tool');
        $this->newLine();

        // Show users without roles
        $usersWithoutRoles = User::whereNull('role_id')->get();
        if ($usersWithoutRoles->count() > 0) {
            $this->warn("⚠️  Users without roles:");
            foreach ($usersWithoutRoles as $user) {
                $this->line("   - {$user->name} ({$user->email})");
            }
            $this->newLine();
        }

        // Show all users with their current roles
        $this->info("📋 Current User Roles:");
        $users = User::with('role')->get();
        
        $table = [];
        foreach ($users as $user) {
            $table[] = [
                'ID' => $user->id,
                'Name' => $user->name,
                'Email' => $user->email,
                'Current Role' => $user->role ? $user->role->display_name : 'No Role',
                'Role Name' => $user->role ? $user->role->name : 'none',
            ];
        }

        $this->table(['ID', 'Name', 'Email', 'Current Role', 'Role Name'], $table);
        $this->newLine();

        // Show available roles
        $roles = Role::where('is_active', true)->get();
        $this->info("🔐 Available Roles:");
        foreach ($roles as $role) {
            $userCount = $role->users()->count();
            $this->line("   - {$role->display_name} ({$role->name}) - {$userCount} users");
        }
        $this->newLine();

        // Interactive assignment
        if ($this->confirm('Do you want to assign roles to users?')) {
            while (true) {
                $userId = $this->ask('Enter user ID (or "exit" to quit)');
                
                if (strtolower($userId) === 'exit') {
                    break;
                }

                $user = User::find($userId);
                if (!$user) {
                    $this->error("User with ID {$userId} not found.");
                    continue;
                }

                $this->info("Selected user: {$user->name} ({$user->email})");
                $currentRole = $user->role ? $user->role->display_name : 'No Role';
                $this->line("Current role: {$currentRole}");

                $roleChoices = $roles->pluck('name', 'display_name')->toArray();
                $roleChoices['none'] = 'Remove Role';
                
                $selectedRole = $this->choice(
                    'Select a role for this user:',
                    array_keys($roleChoices)
                );

                if ($selectedRole === 'Remove Role') {
                    $user->update(['role_id' => null]);
                    $this->info("✅ Removed role from user '{$user->name}'");
                } else {
                    $roleName = $roleChoices[$selectedRole];
                    $role = Role::where('name', $roleName)->first();
                    
                    $user->update(['role_id' => $role->id]);
                    $this->info("✅ Assigned role '{$role->display_name}' to user '{$user->name}'");
                }

                if (!$this->confirm('Assign another role?')) {
                    break;
                }
            }
        }

        // Auto-assign suggestions
        if ($this->confirm('Do you want to see auto-assignment suggestions?')) {
            $this->suggestRoleAssignments();
        }

        return 0;
    }

    private function suggestRoleAssignments()
    {
        $this->info("💡 Role Assignment Suggestions:");
        $this->newLine();

        $users = User::whereNull('role_id')->get();
        
        if ($users->count() === 0) {
            $this->line("   All users already have roles assigned.");
            return;
        }

        foreach ($users as $user) {
            $suggestion = $this->suggestRoleForUser($user);
            $this->line("   {$user->name} ({$user->email}) → {$suggestion['role']} ({$suggestion['reason']})");
        }

        $this->newLine();
        if ($this->confirm('Apply these suggestions?')) {
            foreach ($users as $user) {
                $suggestion = $this->suggestRoleForUser($user);
                $role = Role::where('name', $suggestion['role_name'])->first();
                
                if ($role) {
                    $user->update(['role_id' => $role->id]);
                    $this->info("✅ Assigned {$role->display_name} to {$user->name}");
                }
            }
        }
    }

    private function suggestRoleForUser($user)
    {
        // Simple heuristics for role assignment
        $email = strtolower($user->email);
        $name = strtolower($user->name);

        // Check for admin indicators
        if (str_contains($email, 'admin') || str_contains($name, 'admin')) {
            return [
                'role' => 'Business Administrator',
                'role_name' => 'business_admin',
                'reason' => 'Contains "admin" in name/email'
            ];
        }

        // Check for sales indicators
        if (str_contains($email, 'sales') || str_contains($name, 'sales')) {
            return [
                'role' => 'Sales Representative',
                'role_name' => 'sales_rep',
                'reason' => 'Contains "sales" in name/email'
            ];
        }

        // Check for supplier indicators
        if (str_contains($email, 'supplier') || str_contains($name, 'supplier')) {
            return [
                'role' => 'Supplier',
                'role_name' => 'supplier',
                'reason' => 'Contains "supplier" in name/email'
            ];
        }

        // Check for manager indicators
        if (str_contains($email, 'manager') || str_contains($name, 'manager')) {
            return [
                'role' => 'Operations Manager',
                'role_name' => 'operations_manager',
                'reason' => 'Contains "manager" in name/email'
            ];
        }

        // Default suggestion
        return [
            'role' => 'Viewer',
            'role_name' => 'viewer',
            'reason' => 'Default safe role'
        ];
    }
}
