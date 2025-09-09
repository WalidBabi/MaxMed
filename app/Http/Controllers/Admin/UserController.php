<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
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
        $query = User::with(['role', 'activeAssignedCategories', 'products']);

        // Tab filtering
        switch ($tab) {
            case 'suppliers':
                $query->whereHas('role', function($q) {
                    $q->where('name', 'supplier');
                });
                break;
            case 'admins':
                $query->whereHas('role', function($q) {
                    $q->where('name', 'admin');
                });
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
                $query->whereHas('role', function($q) {
                    $q->where('name', 'admin');
                });
            } elseif ($request->role === 'no_role') {
                $query->where('role_id', null);
            } else {
                $query->where('role_id', $request->role);
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
        
        // Get counts for tab badges
        $supplierCount = User::whereHas('role', function($q) {
            $q->where('name', 'supplier');
        })->count();
        
        $adminCount = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->count();
        
        // Check if user can view sensitive data
        $canViewSensitive = auth()->user()->can('view-sensitive-data');
        
        return view('admin.users.index', compact('users', 'roles', 'supplierCount', 'adminCount', 'canViewSensitive'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->get();
        
        return view('admin.users.create', compact('roles'));
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
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

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
        
        return view('admin.users.edit', compact('user', 'roles'));
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
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

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