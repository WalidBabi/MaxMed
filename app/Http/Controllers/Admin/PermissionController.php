<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\PermissionManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionManagementService $permissionService)
    {
        $this->middleware('auth');
        $this->middleware('permission:permissions.view')->only(['index', 'show']);
        $this->middleware('permission:permissions.create')->only(['create', 'store']);
        $this->middleware('permission:permissions.edit')->only(['edit', 'update']);
        $this->middleware('permission:permissions.delete')->only(['destroy']);
        
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of permissions
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by active status
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $permissions = $query->orderBy('category')->orderBy('name')->get()->groupBy('category');
        
        // Get categories from actual permissions in database, with fallback to static method
        $permissionCategories = Permission::getCategories();
        $actualCategories = $permissions->keys()->mapWithKeys(function($category) use ($permissionCategories) {
            return [$category => $permissionCategories[$category] ?? ucwords(str_replace('_', ' ', $category))];
        });
        
        $permissionDocumentation = \App\Services\PermissionDocumentationService::getAllPermissionDocumentation();

        return view('admin.permissions.index', compact('permissions', 'actualCategories', 'permissionDocumentation'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        $categories = $this->permissionService->getCategories();
        return view('admin.permissions.create', compact('categories'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Validate permission name format
        if (!preg_match('/^[a-z_]+\.[a-z_]+$/', $request->name)) {
            return back()->withErrors(['name' => 'Permission name must be in format: category.action (e.g., products.create)']);
        }

        try {
            $permission = $this->permissionService->createPermission($request->all());

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create permission: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission)
    {
        $permission->load('roles');
        return view('admin.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(Permission $permission)
    {
        $categories = $this->permissionService->getCategories();
        return view('admin.permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        try {
            $this->permissionService->updatePermission($permission, $request->all());

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update permission: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        try {
            $this->permissionService->deletePermission($permission);

            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission deactivated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete permission: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate permissions for a controller
     */
    public function generateController(Request $request)
    {
        $request->validate([
            'controller' => 'required|string|max:100',
            'actions' => 'required|array|min:1',
            'actions.*' => 'string|in:view,create,edit,delete,approve,manage',
        ]);

        try {
            $permissions = $this->permissionService->generateControllerPermissions(
                $request->controller,
                $request->actions
            );

            $middlewareCode = $this->permissionService->generateMiddlewareCode(
                $request->controller,
                $request->actions
            );

            return response()->json([
                'success' => true,
                'permissions' => $permissions,
                'middleware_code' => $middlewareCode,
                'message' => 'Permissions generated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync permissions with routes
     */
    public function syncRoutes()
    {
        try {
            $result = $this->permissionService->syncPermissionsWithRoutes();

            return response()->json([
                'success' => true,
                'result' => $result,
                'message' => 'Permissions synced with routes successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
