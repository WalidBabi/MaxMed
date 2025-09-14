<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PermissionEnforcementService;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class TestPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:test 
                            {--user= : Test specific user ID}
                            {--role= : Test specific role}
                            {--permission= : Test specific permission}
                            {--audit : Run full audit report}
                            {--fix : Attempt to fix common permission issues}
                            {--export= : Export results to file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and validate permission enforcement across the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Starting Permission Testing Suite...');
        $this->newLine();

        if ($this->option('audit')) {
            $this->runFullAudit();
        } elseif ($this->option('user')) {
            $this->testSpecificUser($this->option('user'));
        } elseif ($this->option('role')) {
            $this->testSpecificRole($this->option('role'));
        } elseif ($this->option('permission')) {
            $this->testSpecificPermission($this->option('permission'));
        } else {
            $this->runComprehensiveTests();
        }

        if ($this->option('export')) {
            $this->exportResults();
        }

        $this->newLine();
        $this->info('✅ Permission testing completed!');
    }

    private function runFullAudit()
    {
        $this->info('🔍 Running Full Permission Audit...');
        
        $progressBar = $this->output->createProgressBar(4);
        $progressBar->start();

        // Test user permissions
        $progressBar->advance();
        $this->info(' Testing user permissions...');
        $userResults = $this->testAllUsers();

        // Test controller enforcement
        $progressBar->advance();
        $this->info(' Testing controller enforcement...');
        $controllerResults = PermissionEnforcementService::testControllerPermissionEnforcement();

        // Test route enforcement
        $progressBar->advance();
        $this->info(' Testing route enforcement...');
        $routeResults = PermissionEnforcementService::testRoutePermissionEnforcement();

        // Test UI controls
        $progressBar->advance();
        $this->info(' Testing UI permission controls...');
        $uiResults = PermissionEnforcementService::testUIPermissionControls();

        $progressBar->finish();
        $this->newLine();

        // Display results
        $this->displayAuditResults($userResults, $controllerResults, $routeResults, $uiResults);

        // Generate recommendations
        $this->generateRecommendations($controllerResults, $routeResults, $uiResults);
    }

    private function runComprehensiveTests()
    {
        $this->info('🧪 Running Comprehensive Permission Tests...');
        
        // Test all users
        $this->testAllUsers();
        
        // Test specific scenarios
        $this->testPermissionScenarios();
        
        // Test edge cases
        $this->testEdgeCases();
    }

    private function testAllUsers()
    {
        $this->info('👥 Testing permissions for all users...');
        
        $users = User::with('role')->get();
        $results = [];
        
        foreach ($users as $user) {
            $userResults = PermissionEnforcementService::testUserPermissions($user);
            $results[] = $userResults;
            
            $this->displayUserResults($userResults);
        }
        
        return $results;
    }

    private function displayUserResults(array $results)
    {
        $user = User::find($results['user_id']);
        $role = $user->role;
        
        $this->line("👤 {$user->name} ({$user->email}) - Role: {$role->name}");
        $this->line("   📊 Tested: {$results['permissions_tested']} permissions");
        $this->line("   ✅ Passed: {$results['permissions_passed']} permissions");
        $this->line("   ❌ Failed: {$results['permissions_failed']} permissions");
        
        if ($results['permissions_failed'] > 0) {
            $this->warn("   ⚠️  {$results['permissions_failed']} permission failures detected");
        }
        
        $this->newLine();
    }

    private function testSpecificUser($userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }
        
        $this->info("👤 Testing permissions for user: {$user->name}");
        
        $results = PermissionEnforcementService::testUserPermissions($user);
        $this->displayUserResults($results);
        
        // Show detailed permission breakdown
        $this->showDetailedPermissions($results);
    }

    private function testSpecificRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->error("Role '{$roleName}' not found.");
            return;
        }
        
        $this->info("🎭 Testing permissions for role: {$role->display_name}");
        
        $users = $role->users;
        if ($users->count() === 0) {
            $this->warn("No users found with role '{$roleName}'");
            return;
        }
        
        foreach ($users as $user) {
            $results = PermissionEnforcementService::testUserPermissions($user);
            $this->displayUserResults($results);
        }
    }

    private function testSpecificPermission($permissionName)
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            $this->error("Permission '{$permissionName}' not found.");
            return;
        }
        
        $this->info("🔑 Testing permission: {$permission->display_name}");
        
        // Find all users with this permission
        $usersWithPermission = User::whereHas('role.permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->get();
        
        $this->line("👥 {$usersWithPermission->count()} users have this permission:");
        
        foreach ($usersWithPermission as $user) {
            $this->line("   • {$user->name} ({$user->email}) - {$user->role->name}");
        }
        
        // Find all users without this permission
        $usersWithoutPermission = User::whereDoesntHave('role.permissions', function($query) use ($permissionName) {
            $query->where('name', $permissionName);
        })->get();
        
        $this->line("🚫 {$usersWithoutPermission->count()} users DON'T have this permission:");
        
        foreach ($usersWithoutPermission as $user) {
            $this->line("   • {$user->name} ({$user->email}) - {$user->role->name}");
        }
    }

    private function showDetailedPermissions(array $results)
    {
        $this->newLine();
        $this->info('📋 Detailed Permission Breakdown:');
        
        $failed = array_filter($results['details'], function($detail) {
            return !$detail['has_permission'];
        });
        
        if (count($failed) > 0) {
            $this->error('❌ Failed Permissions:');
            foreach ($failed as $detail) {
                $this->line("   • {$detail['permission']} - {$detail['display_name']} ({$detail['category']})");
            }
        }
        
        $passed = array_filter($results['details'], function($detail) {
            return $detail['has_permission'];
        });
        
        if (count($passed) > 0) {
            $this->info('✅ Passed Permissions:');
            foreach (array_slice($passed, 0, 10) as $detail) {
                $this->line("   • {$detail['permission']} - {$detail['display_name']}");
            }
            
            if (count($passed) > 10) {
                $this->line("   ... and " . (count($passed) - 10) . " more");
            }
        }
    }

    private function testPermissionScenarios()
    {
        $this->info('🎯 Testing Permission Scenarios...');
        
        // Test admin access scenarios
        $this->testAdminScenarios();
        
        // Test supplier access scenarios
        $this->testSupplierScenarios();
        
        // Test customer access scenarios
        $this->testCustomerScenarios();
    }

    private function testAdminScenarios()
    {
        $this->line('🔧 Testing Admin Scenarios...');
        
        $adminUsers = User::whereHas('role', function($query) {
            $query->whereIn('name', ['admin', 'super_admin']);
        })->get();
        
        foreach ($adminUsers as $user) {
            $canManageUsers = $user->hasAnyPermission(['users.create', 'users.edit', 'users.delete']);
            $canManageRoles = $user->hasAnyPermission(['roles.create', 'roles.edit', 'roles.delete']);
            $canAccessSystem = $user->hasPermission('system.settings');
            
            $this->line("   👤 {$user->name}: Users({$canManageUsers}) | Roles({$canManageRoles}) | System({$canAccessSystem})");
        }
    }

    private function testSupplierScenarios()
    {
        $this->line('🏭 Testing Supplier Scenarios...');
        
        $supplierUsers = User::whereHas('role', function($query) {
            $query->where('name', 'supplier');
        })->get();
        
        foreach ($supplierUsers as $user) {
            $canManageProducts = $user->hasAnyPermission(['supplier.products.create', 'supplier.products.edit']);
            $canViewOrders = $user->hasPermission('supplier.orders.view');
            $canManageOrders = $user->hasPermission('supplier.orders.manage');
            
            $this->line("   👤 {$user->name}: Products({$canManageProducts}) | View Orders({$canViewOrders}) | Manage Orders({$canManageOrders})");
        }
    }

    private function testCustomerScenarios()
    {
        $this->line('🛒 Testing Customer Scenarios...');
        
        $customerUsers = User::whereHas('role', function($query) {
            $query->where('name', 'customer');
        })->get();
        
        foreach ($customerUsers as $user) {
            $canViewProducts = $user->hasPermission('products.view');
            $canCreateOrders = $user->hasPermission('orders.create');
            $canViewOwnOrders = $user->hasPermission('orders.view_own');
            $canAccessAdmin = $user->hasPermission('dashboard.admin');
            
            $this->line("   👤 {$user->name}: View Products({$canViewProducts}) | Create Orders({$canCreateOrders}) | View Orders({$canViewOwnOrders}) | Admin Access({$canAccessAdmin})");
        }
    }

    private function testEdgeCases()
    {
        $this->info('🔍 Testing Edge Cases...');
        
        // Test users without roles
        $usersWithoutRoles = User::whereNull('role_id')->count();
        if ($usersWithoutRoles > 0) {
            $this->warn("⚠️  Found {$usersWithoutRoles} users without assigned roles");
        }
        
        // Test inactive permissions
        $inactivePermissions = Permission::where('is_active', false)->count();
        if ($inactivePermissions > 0) {
            $this->info("📝 Found {$inactivePermissions} inactive permissions");
        }
        
        // Test orphaned role-permission relationships
        $orphanedRelations = \DB::table('role_permissions')
            ->whereNotIn('permission_id', Permission::pluck('id'))
            ->count();
        
        if ($orphanedRelations > 0) {
            $this->warn("⚠️  Found {$orphanedRelations} orphaned role-permission relationships");
        }
    }

    private function displayAuditResults($userResults, $controllerResults, $routeResults, $uiResults)
    {
        $this->newLine();
        $this->info('📊 AUDIT RESULTS SUMMARY');
        $this->line('═' . str_repeat('═', 50));
        
        // User Results
        $totalPermissionsTested = array_sum(array_column($userResults, 'permissions_tested'));
        $totalPermissionsPassed = array_sum(array_column($userResults, 'permissions_passed'));
        $totalPermissionsFailed = array_sum(array_column($userResults, 'permissions_failed'));
        
        $this->line("👥 USERS: {$totalPermissionsTested} permissions tested, {$totalPermissionsPassed} passed, {$totalPermissionsFailed} failed");
        
        // Controller Results
        $this->line("🎮 CONTROLLERS: {$controllerResults['controllers_tested']} tested, {$controllerResults['controllers_with_permissions']} with permissions, {$controllerResults['controllers_without_permissions']} without permissions");
        
        // Route Results
        $this->line("🛣️  ROUTES: {$routeResults['routes_tested']} tested, {$routeResults['routes_with_permissions']} with permissions, {$routeResults['routes_without_permissions']} without permissions");
        
        // UI Results
        $this->line("🎨 VIEWS: {$uiResults['views_tested']} tested, {$uiResults['views_with_permissions']} with permissions, {$uiResults['views_without_permissions']} without permissions");
        
        $this->newLine();
    }

    private function generateRecommendations($controllerResults, $routeResults, $uiResults)
    {
        $this->info('💡 RECOMMENDATIONS');
        $this->line('═' . str_repeat('═', 50));
        
        if ($controllerResults['controllers_without_permissions'] > 0) {
            $this->error("🔴 CRITICAL: {$controllerResults['controllers_without_permissions']} controllers lack permission enforcement");
            $this->line("   → Add permission middleware to all admin controllers");
        }
        
        if ($routeResults['routes_without_permissions'] > 0) {
            $this->error("🔴 CRITICAL: {$routeResults['routes_without_permissions']} admin routes lack permission middleware");
            $this->line("   → Add permission middleware to all admin routes");
        }
        
        if ($uiResults['views_without_permissions'] > 0) {
            $this->warn("🟡 WARNING: {$uiResults['views_without_permissions']} views lack permission checks");
            $this->line("   → Add permission checks to sensitive UI elements");
        }
        
        $this->newLine();
    }

    private function exportResults()
    {
        $filename = $this->option('export');
        $report = PermissionEnforcementService::generateAuditReport();
        
        file_put_contents($filename, json_encode($report, JSON_PRETTY_PRINT));
        
        $this->info("📄 Results exported to: {$filename}");
    }
}
