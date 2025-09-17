<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\PermissionDocumentationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['create', 'store']);
        $this->middleware('permission:roles.edit')->only(['edit', 'update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availablePermissions = Role::getAvailablePermissions();
        $permissionCategories = Permission::getCategories();
        $permissions = $this->getOrderedPermissions();
        $permissionDocumentation = PermissionDocumentationService::getAllPermissionDocumentation();
        
        return view('admin.roles.create', compact('availablePermissions', 'permissionCategories', 'permissions', 'permissionDocumentation'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
        ]);

        $role = Role::create([
            'name' => Str::slug($request->display_name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => [], // Keep for backward compatibility
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sync permissions using the new system
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('users');
        $rolePermissions = $role->permissions()->where('is_active', true)->get();
        $availablePermissions = Role::getAvailablePermissions();
        $permissionCategories = Permission::getCategories();
        $permissions = $this->getOrderedPermissions();
        
        return view('admin.roles.show', compact('role', 'availablePermissions', 'permissionCategories', 'permissions', 'rolePermissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        // Load permissions using the relationship method instead of eager loading
        $rolePermissions = $role->permissions()->where('is_active', true)->get();
        $availablePermissions = Role::getAvailablePermissions();
        $permissionCategories = Permission::getCategories();
        $permissions = $this->getOrderedPermissions();
        $permissionDocumentation = PermissionDocumentationService::getAllPermissionDocumentation();
        
        return view('admin.roles.edit', compact('role', 'rolePermissions', 'availablePermissions', 'permissionCategories', 'permissions', 'permissionDocumentation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
        ]);

        $role->update([
            'name' => Str::slug($request->display_name),
            'display_name' => $request->display_name,
            'description' => $request->description,
            'permissions' => [], // Keep for backward compatibility
            'is_active' => $request->boolean('is_active', true),
        ]);
        
        // Sync permissions using the new system
        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->sync([]);
        }

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot delete role that has users assigned to it.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role status
     */
    public function toggleStatus(Role $role)
    {
        $role->update([
            'is_active' => !$role->is_active
        ]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.roles.index')
            ->with('success', "Role {$status} successfully.");
    }

    /**
     * Get permissions ordered consistently by category and display name
     */
    private function getOrderedPermissions()
    {
        $permissionCategories = Permission::getCategories();
        
        // Get permissions ordered consistently: by category first, then by display_name within each category
        $allPermissions = Permission::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_name')
            ->get();
        
        // Group permissions by category while maintaining order
        $permissions = collect();
        $categoryOrder = array_keys($permissionCategories);
        
        foreach ($categoryOrder as $category) {
            $categoryPermissions = $allPermissions->where('category', $category);
            if ($categoryPermissions->isNotEmpty()) {
                $permissions->put($category, $categoryPermissions->values());
            }
        }
        
        // Add any categories not in the predefined list (for dynamic categories)
        $remainingCategories = $allPermissions->pluck('category')->unique()->diff($categoryOrder);
        foreach ($remainingCategories as $category) {
            $categoryPermissions = $allPermissions->where('category', $category);
            if ($categoryPermissions->isNotEmpty()) {
                $permissions->put($category, $categoryPermissions->values());
            }
        }
        
        return $permissions;
    }
} 