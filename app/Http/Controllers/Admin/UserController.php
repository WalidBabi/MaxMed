<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.create')->only(['create', 'store']);
        $this->middleware('permission:users.edit')->only(['edit', 'update']);
        $this->middleware('permission:users.delete')->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        
        // Base query with relationships
        $with = ['role', 'activeAssignedCategories', 'products'];
        try {
            if (Schema::hasTable('role_user')) {
                $with[] = 'roles';
            }
        } catch (\Throwable $e) {}
        $query = User::with($with);

        // Tab filtering
        switch ($tab) {
            case 'suppliers':
                try {
                    if (Schema::hasTable('role_user')) {
                        $query->whereHas('roles', function($q) { $q->where('name', 'supplier'); });
                        break;
                    }
                } catch (\Throwable $e) {}
                $query->whereHas('role', function($q) { $q->where('name', 'supplier'); });
                break;
            case 'admins':
                try {
                    if (Schema::hasTable('role_user')) {
                        $query->whereHas('roles', function($q) { $q->whereIn('name', ['admin','super_admin']); });
                        break;
                    }
                } catch (\Throwable $e) {}
                $query->whereHas('role', function($q) { $q->whereIn('name', ['admin','super_admin']); });
                break;
            default:
                // All users - no additional filtering
                break;
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role filter (only apply if not in specific tab)
        if ($request->filled('role') && $tab === 'all') {
            if ($request->role === 'admin') {
                try {
                    if (Schema::hasTable('role_user')) {
                        $query->whereHas('roles', function($q) { $q->whereIn('name', ['admin','super_admin']); });
                    } else {
                        $query->whereHas('role', function($q) { $q->whereIn('name', ['admin','super_admin']); });
                    }
                } catch (\Throwable $e) {
                    $query->whereHas('role', function($q) { $q->whereIn('name', ['admin','super_admin']); });
                }
            } elseif ($request->role === 'no_role') {
                $query->where('role_id', null);
            } else {
                try {
                    if (Schema::hasTable('role_user')) {
                        $query->whereHas('roles', function($q) use ($request) { $q->where('roles.id', $request->role); });
                    } else {
                        $query->where('role_id', $request->role);
                    }
                } catch (\Throwable $e) {
                    $query->where('role_id', $request->role);
                }
            }
        }

        // Status filter (for future use when status field is added)
        if ($request->filled('status')) {
            // Add status filtering logic here when status field is implemented
            // $query->where('status', $request->status);
        }

        // Sorting
        $sort = $request->get('sort', 'created_desc');
        switch ($sort) {
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('email', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $users = $query->paginate(10)->appends($request->query());
        
        // Get roles for the filter dropdown
        $roles = Role::where('is_active', true)->get();
        
        // Whether multi-role pivot exists (for view rendering)
        $hasRolePivot = false;
        try {
            $hasRolePivot = Schema::hasTable('role_user');
        } catch (\Throwable $e) {}
        
        // Get counts for tab badges
        $supplierCount = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->count();
        
        $adminCount = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->count();
        
        // Check if user can view sensitive data
        $canViewSensitive = auth()->user()->can('view-sensitive-data');
        
        return view('admin.users.index', compact('users', 'roles', 'supplierCount', 'adminCount', 'canViewSensitive', 'hasRolePivot'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        $assignedRoleIds = [];
        
        return view('admin.users.create', compact('roles', 'assignedRoleIds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // Backward compatibility: set role_id to first selected role if provided
            'role_id' => ($request->filled('roles') && is_array($request->roles)) ? ($request->roles[0] ?? null) : null,
        ]);

        // Sync roles to pivot if available
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('role_user') && $request->filled('roles') && is_array($request->roles)) {
                $user->roles()->sync($request->roles);
            }
        } catch (\Throwable $e) {
            // Ignore if pivot missing; legacy role_id remains
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->get();
        $assignedRoleIds = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('role_user')) {
                $assignedRoleIds = $user->roles()->pluck('roles.id')->toArray();
            } elseif ($user->role_id) {
                $assignedRoleIds = [$user->role_id];
            }
        } catch (\Throwable $e) {
            if ($user->role_id) {
                $assignedRoleIds = [$user->role_id];
            }
        }
        
        return view('admin.users.edit', compact('user', 'roles', 'assignedRoleIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            // Backward compatibility: set role_id to first selected role if provided
            'role_id' => ($request->has('roles') && is_array($request->roles) && count($request->roles) > 0)
                ? ($request->roles[0] ?? null)
                : $user->role_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Ensure both legacy role_id and submitted roles are reflected in pivot (union)
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('role_user')) {
                $submitted = $request->has('roles') && is_array($request->roles)
                    ? array_map('intval', $request->roles)
                    : [];
                $existing = $user->roles()->pluck('roles.id')->toArray();
                $legacy = $user->role_id ? [$user->role_id] : [];
                $final = array_values(array_unique(array_merge($existing, $legacy, $submitted)));
                if (!empty($final)) {
                    $user->roles()->sync($final);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if pivot missing; legacy role_id remains
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting the currently authenticated user
        if (Auth::user()->id === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
} 