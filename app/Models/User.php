<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'is_admin',
        'role_id',
        'profile_photo',
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
            'is_admin' => 'boolean',
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
        return $this->is_admin === true || ($this->role && $this->role->hasPermission('dashboard.view'));
    }

    /**
     * Check if the user has a specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->is_admin) {
            return true; // Super admin has all permissions
        }

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
     * Get the supplier category assignments
     */
    public function supplierCategories()
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
     * Check if user is a supplier
     */
    public function isSupplier(): bool
    {
        return $this->role && $this->role->name === 'supplier';
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
     * Get supplier performance metrics for a category
     */
    public function getCategoryPerformance($categoryId)
    {
        return $this->supplierCategories()
                    ->where('category_id', $categoryId)
                    ->first();
    }

    /**
     * Get overall supplier performance score
     */
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
        return strtoupper(substr($this->name ?? 'U', 0, 2));
    }
}
