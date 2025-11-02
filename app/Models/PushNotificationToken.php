<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PushNotificationToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'name',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a new push notification token
     * Returns array with 'token' (plain) and 'model' (database record)
     */
    public static function generateToken(User $user, ?string $name = null, int $expiresInDays = 365): array
    {
        $plainToken = Str::random(32);
        $hashedToken = hash('sha256', $plainToken);
        
        $model = self::create([
            'user_id' => $user->id,
            'token' => $hashedToken,
            'name' => $name,
            'expires_at' => now()->addDays($expiresInDays),
        ]);
        
        return [
            'token' => $plainToken,
            'model' => $model,
        ];
    }

    /**
     * Check if token is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Update last used timestamp
     */
    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }
}
