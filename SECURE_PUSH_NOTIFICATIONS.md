# Secure Push Notifications with API Tokens

## Problem

The challenge was to enable push notifications for mobile users without compromising security by extending session lifetimes. Traditional long-term sessions increase security risks:

- 🔴 Session hijacking
- 🔴 XSS/CSRF vulnerabilities
- 🔴 Device theft exposure
- 🔴 Compliance violations (HIPAA, PCI-DSS)

## Solution

Implemented a **dedicated API token system** for push notifications that:
- ✅ Keeps session lifetimes secure (2 hours)
- ✅ Provides long-term access for push notifications (1 year)
- ✅ Isolated scope (only push subscriptions)
- ✅ Revocable and trackable
- ✅ No exposure to regular web session vulnerabilities

## Architecture

### Components

1. **PushNotificationToken Model** - Stores secure tokens
2. **AuthenticatePushToken Middleware** - Validates tokens
3. **PushSubscriptionController** - Manages tokens and subscriptions
4. **Dedicated Routes** - Separate auth for push endpoints

### Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. User Logs In (Regular Auth)                                  │
│    ├─ Session: 2 hours (secure)                                 │
│    └─ User authenticated via session                            │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 2. Generate Push Token (While Authenticated)                    │
│    POST /push/generate-token                                    │
│    ├─ Requires: Active session                                  │
│    └─ Returns: API token (plain, shown once)                    │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 3. Subscribe to Push Notifications                              │
│    POST /push/subscribe                                         │
│    Headers: Authorization: Bearer {token}                       │
│    ├─ Validates: API token (hashed in DB)                       │
│    ├─ Scope: Push subscriptions only                            │
│    └─ Expires: 1 year from creation                             │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ 4. Long-Term Access (After Session Expires)                     │
│    ├─ Session: Expired (2 hours)                                │
│    ├─ Push Token: Valid (1 year)                                │
│    └─ Users receive notifications without re-login              │
└─────────────────────────────────────────────────────────────────┘
```

## Implementation

### 1. Database Schema

**Table:** `push_notification_tokens`

```php
Schema::create('push_notification_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('token', 64)->unique();  // Hashed token
    $table->string('name')->nullable();     // Device name
    $table->timestamp('last_used_at')->nullable();
    $table->timestamp('expires_at')->nullable();
    $table->timestamps();
    
    $table->index('user_id');
    $table->index('token');
});
```

### 2. Security Features

**Token Generation:**
```php
// Generate 32-character random string
$plainToken = Str::random(32);

// Hash with SHA-256 for storage
$hashedToken = hash('sha256', $plainToken);

// Store only hash in database
PushNotificationToken::create([
    'user_id' => $user->id,
    'token' => $hashedToken,
    'expires_at' => now()->addDays(365),
]);
```

**Token Validation:**
```php
// Middleware hashes incoming token
$hashedToken = hash('sha256', $token);

// Match against database
$pushToken = PushNotificationToken::where('token', $hashedToken)->first();

// Check expiration
if ($pushToken->isExpired()) {
    return 401;
}
```

### 3. Authentication Options

**Option A: Session-based (Standard Routes)**
```
Route::middleware('auth')->group(function () {
    POST /push/generate-token
    POST /push/subscribe
    DELETE /push/unsubscribe
});
```

**Option B: Token-based (API Routes)**
```
Route::middleware('push.token')->group(function () {
    POST /push/subscribe
    DELETE /push/unsubscribe
});
```

### 4. Token Usage

**Generating a Token:**
```javascript
// While logged in with session
const response = await fetch('/push/generate-token', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    }
});

const { token } = await response.json();
// Store in localStorage or secure storage
localStorage.setItem('push_token', token);
```

**Using the Token:**
```javascript
// Later, when subscribing
const token = localStorage.getItem('push_token');

await fetch('/push/subscribe', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        // OR
        'X-Push-Token': token
    },
    body: JSON.stringify(subscription)
});
```

## API Endpoints

### Generate Token
```http
POST /push/generate-token
Authorization: Session cookie
Content-Type: application/json

{
    "device_name": "Chrome on iPhone"
}

Response: 200 OK
{
    "token": "abc123...",
    "expires_at": "2026-11-02T13:28:56.000000Z",
    "device_name": "Chrome on iPhone"
}
```

### Subscribe to Push
```http
POST /push/subscribe
Authorization: Bearer {token}
Content-Type: application/json

{
    "endpoint": "https://...",
    "keys": {
        "auth": "...",
        "p256dh": "..."
    }
}

Response: 200 OK
{
    "status": "subscribed"
}
```

### Unsubscribe
```http
DELETE /push/unsubscribe
Authorization: Bearer {token}
Content-Type: application/json

{
    "endpoint": "https://..."
}

Response: 200 OK
{
    "status": "unsubscribed"
}
```

## Security Benefits

### Session Security
- ✅ Sessions expire after 2 hours (standard)
- ✅ No long-term session exposure
- ✅ Reduced XSS/CSRF risk
- ✅ Compliance-friendly

### Token Security
- ✅ Scoped to push subscriptions only
- ✅ SHA-256 hashed storage
- ✅ Expiration tracking
- ✅ Revocable per device
- ✅ Usage tracking (last_used_at)
- ✅ Limited scope (can't access web features)

### Comparison

| Aspect | Long Sessions | API Tokens |
|--------|--------------|------------|
| Session Duration | 1 year | 2 hours |
| Push Notifications | ✅ Works | ✅ Works |
| Security Risk | 🔴 High | 🟢 Low |
| Session Hijacking | 🔴 High | 🟢 Low |
| Scope | All features | Push only |
| Revocable | Manual | Automatic |
| Trackable | Limited | Full |
| Compliance | ⚠️ Issues | ✅ Compliant |

## Configuration

**Session Lifetime (Secure):**
```env
SESSION_LIFETIME=120  # 2 hours
```

**Token Lifetime:**
```php
// In PushNotificationToken model
PushNotificationToken::generateToken($user, $name, 365); // 1 year
```

**Token Storage:**
- Plain token shown **once** during generation
- Immediately hashed for database storage
- Never transmitted in plain text again
- Client must store securely

## Best Practices

### Frontend
1. Store token in `localStorage` or secure storage
2. Generate token on first login
3. Refresh if expired
4. Clear on logout

### Backend
1. Hash all tokens in database
2. Validate expiration on every request
3. Track last usage
4. Log security events

### Monitoring
```sql
-- Find expired tokens
SELECT * FROM push_notification_tokens 
WHERE expires_at < NOW();

-- Find unused tokens
SELECT * FROM push_notification_tokens 
WHERE last_used_at IS NULL 
  OR last_used_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- User token count
SELECT user_id, COUNT(*) as token_count
FROM push_notification_tokens
WHERE expires_at > NOW()
GROUP BY user_id;
```

## Migration from Long Sessions

If you previously used long sessions:

1. **Existing Users:** Push notifications continue working
2. **New Users:** Will get tokens on next login
3. **No Disruption:** Seamless transition
4. **No Data Loss:** Existing subscriptions preserved

## Cleanup Tasks

Consider implementing a scheduled job to clean up:
- Expired tokens
- Unused tokens (90+ days)
- Multiple tokens per user (keep latest 3)

```php
// Example cleanup command
php artisan push:tokens:cleanup

// Or in scheduler
$schedule->command('push:tokens:cleanup')->daily();
```

## Testing

### Test Token Generation
```bash
curl -X POST https://maxmedme.com/push/generate-token \
  -H "Cookie: laravel_session=..." \
  -H "X-CSRF-TOKEN: ..."
```

### Test Token Usage
```bash
curl -X POST https://maxmedme.com/push/subscribe \
  -H "Authorization: Bearer {token}" \
  -d '{"endpoint":"...","keys":{...}}'
```

### Test Expiration
```php
// In tests
$token = PushNotificationToken::generateToken($user);
$token->update(['expires_at' => now()->subDay()]);

$response = $this->withHeader('Authorization', "Bearer {$plainToken}")
    ->post('/push/subscribe', []);

$response->assertStatus(401);
```

## Future Enhancements

- [ ] Token rotation mechanism
- [ ] Device fingerprinting
- [ ] Push notification analytics
- [ ] Admin token management UI
- [ ] Automated cleanup jobs
- [ ] Token usage notifications

## Troubleshooting

### Token Not Working
1. Check if expired: `SELECT * FROM push_notification_tokens WHERE token = '...'`
2. Verify hash: Token should be SHA-256 hashed
3. Check logs: Look for "Invalid push notification token"
4. Verify storage: Ensure client stored token correctly

### Too Many Tokens
```sql
-- Find users with many tokens
SELECT user_id, COUNT(*) as count
FROM push_notification_tokens
WHERE expires_at > NOW()
GROUP BY user_id
HAVING count > 5;
```

### Performance
- Tokens are indexed on `user_id` and `token`
- Hashed lookups are fast
- Consider caching active tokens

## Conclusion

This solution provides the **best of both worlds**:
- 🟢 Secure, short-lived sessions
- 🟢 Long-term push notification access
- 🟢 Isolated, scoped tokens
- 🟢 Compliance-ready
- 🟢 User-friendly
- 🟢 Production-ready

Users remain secure while enjoying uninterrupted push notifications!

