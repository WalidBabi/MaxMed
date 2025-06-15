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
