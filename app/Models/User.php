<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'profile_photo',
        'verification_reminder_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the customer associated with the user.
     */
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    /**
     * Check if the user is an admin
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        Log::info('User::isAdmin() - Starting admin check', [
            'user_id' => $this->id,
            'user_email' => $this->email,
            'role_id' => $this->role_id,
            'has_role_relation' => $this->role ? 'yes' : 'no'
        ]);
        
        try {
            if (!$this->role) {
                Log::info('User::isAdmin() - No role found', [
                    'user_id' => $this->id,
                    'role_id' => $this->role_id,
                    'result' => false
                ]);
                return false;
            }
            
            Log::info('User::isAdmin() - Role found, checking permissions', [
                'user_id' => $this->id,
                'role_name' => $this->role->name,
                'role_id' => $this->role->id,
                'role_permissions' => $this->role->permissions
            ]);
            
            $hasPermission = $this->role->hasPermission('dashboard.view');
            
            Log::info('User::isAdmin() - Permission check completed', [
                'user_id' => $this->id,
                'role_name' => $this->role->name,
                'has_dashboard_view' => $hasPermission,
                'result' => $hasPermission
            ]);
            
            return $hasPermission;
            
        } catch (\Exception $e) {
            Log::error('User::isAdmin() - Exception occurred', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return false as fallback
            return false;
        }
    }

    /**
     * Check if the user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    /**
     * Check if the user has any of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the supplier information associated with the user.
     */
    public function supplierInformation(): HasOne
    {
        return $this->hasOne(SupplierInformation::class);
    }

    /**
     * Get the supplier categories assigned to the user.
     */
    public function supplierCategories(): HasMany
    {
        return $this->hasMany(SupplierCategory::class, 'supplier_id');
    }

    /**
     * Get the active supplier category assignments
     */
    public function activeSupplierCategories()
    {
        return $this->hasMany(SupplierCategory::class, 'supplier_id')
                    ->where('status', 'active');
    }

    /**
     * Get the categories this supplier is assigned to
     */
    public function assignedCategories()
    {
        return $this->belongsToMany(Category::class, 'supplier_categories', 'supplier_id', 'category_id')
                    ->withPivot([
                        'id', 'status', 'minimum_order_value', 'lead_time_days', 'notes', 
                        'commission_rate', 'avg_response_time_hours', 'quotation_win_rate',
                        'total_quotations', 'won_quotations', 'avg_customer_rating',
                        'assigned_by', 'assigned_at', 'last_quotation_at', 'created_at', 'updated_at'
                    ])
                    ->withTimestamps();
    }

    /**
     * Get the active categories this supplier is assigned to
     */
    public function activeAssignedCategories()
    {
        return $this->belongsToMany(Category::class, 'supplier_categories', 'supplier_id', 'category_id')
                    ->wherePivot('status', 'active')
                    ->withPivot(['status', 'assigned_at'])
                    ->withTimestamps();
    }

    /**
     * Get supplier products
     */
    public function supplierProducts()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Get products (alias for supplierProducts for consistency)
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    /**
     * Get the supplier's brand (based on company name)
     */
    public function supplierBrand()
    {
        return $this->hasOne(Brand::class, 'name', 'supplierInformation.company_name');
    }

    /**
     * Get or create the supplier's brand based on their company name
     */
    public function getOrCreateSupplierBrand()
    {
        if (!$this->isSupplier() || !$this->supplierInformation) {
            return null;
        }

        // First check if supplier has a specific brand assigned
        if ($this->supplierInformation->brand_id) {
            return $this->supplierInformation->brand;
        }

        // Fall back to company name
        $companyName = $this->supplierInformation->company_name;
        if (empty($companyName)) {
            return null;
        }

        return Brand::firstOrCreate(['name' => $companyName]);
    }

    /**
     * Check if user is a supplier
     */
    public function isSupplier(): bool
    {
        Log::info('User::isSupplier() - Starting supplier check', [
            'user_id' => $this->id,
            'user_email' => $this->email,
            'role_id' => $this->role_id,
            'has_role_relation' => $this->role ? 'yes' : 'no'
        ]);

        try {
            if (!$this->role) {
                Log::info('User::isSupplier() - No role found', [
                    'user_id' => $this->id,
                    'role_id' => $this->role_id,
                    'result' => false
                ]);
                return false;
            }

            Log::info('User::isSupplier() - Role found, checking role name', [
                'user_id' => $this->id,
                'role_name' => $this->role->name,
                'role_id' => $this->role->id
            ]);

            $isSupplier = $this->role->name === 'supplier';

            Log::info('User::isSupplier() - Supplier check completed', [
                'user_id' => $this->id,
                'role_name' => $this->role->name,
                'is_supplier' => $isSupplier,
                'result' => $isSupplier
            ]);

            return $isSupplier;

        } catch (\Exception $e) {
            Log::error('User::isSupplier() - Exception occurred', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return false as fallback
            return false;
        }
    }

    /**
     * Check if supplier is assigned to a specific category
     */
    public function isAssignedToCategory($categoryId): bool
    {
        return $this->supplierCategories()
                    ->where('category_id', $categoryId)
                    ->where('status', 'active')
                    ->exists();
    }

    /**
     * Send the email verification notification using custom template with logo.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }

    /**
     * Send the password reset notification using custom template with logo.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPassword($token));
    }


    public function getOverallPerformanceScoreAttribute(): float
    {
        $assignments = $this->activeSupplierCategories;
        
        if ($assignments->isEmpty()) {
            return 0.0;
        }

        $totalScore = $assignments->sum(function ($assignment) {
            $winRateScore = $assignment->quotation_win_rate;
            $responseTimeScore = max(0, 100 - ($assignment->avg_response_time_hours / 48 * 100));
            $ratingScore = ($assignment->avg_customer_rating / 5) * 100;
            
            return ($winRateScore + $responseTimeScore + $ratingScore) / 3;
        });

        return $totalScore / $assignments->count();
    }

    /**
     * Get the profile photo URL
     *
     * @return string|null
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        return null;
    }

    /**
     * Get profile photo or default initials
     *
     * @return string
     */
    public function getProfileDisplayAttribute()
    {
        if ($this->profile_photo) {
            return $this->profile_photo_url;
        }
        
        // Generate initials from name
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}
