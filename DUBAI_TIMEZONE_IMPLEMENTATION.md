# Dubai Timezone Implementation Guide

This document explains how to implement and use Dubai timezone (Asia/Dubai) throughout the MaxMed application.

## Overview

All dates and times in the application are now displayed in Dubai timezone instead of UTC. This ensures consistency for local users and business operations.

## Configuration Changes

### 1. Laravel Configuration
- **File**: `config/app.php`
- **Change**: Default timezone set to `'Asia/Dubai'`
- **Environment**: Update `.env` file: `APP_TIMEZONE=Asia/Dubai`

### 2. Carbon Default Timezone
- **File**: `app/Providers/AppServiceProvider.php`
- **Addition**: `Carbon::setDefaultTimezone('Asia/Dubai')`

## PHP/Blade Usage

### Helper Functions
Three global helper functions are available:

```php
// Format any date to Dubai timezone
formatDubaiDate($date, 'M d, Y H:i')

// Get human-readable time difference in Dubai timezone
formatDubaiDateForHumans($date)

// Get current time in Dubai timezone
nowDubai('M d, Y H:i')
```

### Model Trait
The `DubaiDateFormat` trait provides additional methods for models:

```php
// Add to any model
use App\Traits\DubaiDateFormat;

class Order extends Model
{
    use DubaiDateFormat;
    
    // Usage examples:
    // $order->createdAtDubai()
    // $order->updatedAtDubai()
    // $order->createdAtDubaiForHumans()
    // $order->formatForDubai('created_at', 'F j, Y g:i A')
}
```

### Blade Component
Use the reusable blade component for consistent formatting:

```blade
<!-- Basic usage -->
<x-dubai-date :date="$order->created_at" />

<!-- Custom format -->
<x-dubai-date :date="$order->created_at" format="F j, Y g:i A" />

<!-- Without timezone indicator -->
<x-dubai-date :date="$order->created_at" :show-timezone="false" />

<!-- Date only (no time) -->
<x-dubai-date :date="$order->created_at" :show-time="false" />
```

## JavaScript Usage

### Available Functions
Client-side JavaScript functions for dynamic content:

```javascript
// Format date with time
formatDubaiDateTime(dateString)

// Format date only
formatDubaiDateOnly(dateString)

// Relative time (e.g., "2 hours ago")
formatTimeAgo(dateString)

// Current Dubai time
nowDubai()
```

### Example Usage
```javascript
// Update timestamps dynamically
document.querySelector('#timestamp').textContent = formatDubaiDateTime(notification.created_at);

// Show relative times
document.querySelector('#relative-time').textContent = formatTimeAgo(order.created_at);
```

## Migration Examples

### Before (UTC)
```blade
{{ $order->created_at->format('M d, Y H:i') }}
{{ $feedback->created_at->diffForHumans() }}
```

### After (Dubai Time)
```blade
{{ formatDubaiDate($order->created_at, 'M d, Y H:i') }}
{{ formatDubaiDateForHumans($feedback->created_at) }}

<!-- Or using the component -->
<x-dubai-date :date="$order->created_at" format="M d, Y H:i" />
```

### JavaScript Migration
```javascript
// Before
notification.created_at

// After
formatDubaiDateTime(notification.created_at)
```

## Common Format Patterns

| Pattern | Example Output |
|---------|----------------|
| `M d, Y` | Dec 15, 2024 |
| `M d, Y H:i` | Dec 15, 2024 14:30 |
| `F j, Y g:i A` | December 15, 2024 2:30 PM |
| `d/m/Y` | 15/12/2024 |
| `Y-m-d H:i:s` | 2024-12-15 14:30:00 |

## Files Updated

### Core Files
- `config/app.php` - Set default timezone
- `app/Providers/AppServiceProvider.php` - Carbon timezone
- `app/Helpers/DateHelper.php` - Helper functions
- `app/Traits/DubaiDateFormat.php` - Model trait
- `resources/views/components/dubai-date.blade.php` - Blade component
- `public/js/dubai-date.js` - JavaScript utilities
- `resources/views/layouts/app.blade.php` - Include JS file

### Example Template Updates
- `resources/views/orders/show.blade.php`
- `resources/views/orders/index.blade.php`
- `resources/views/admin/orders/index.blade.php`

## Production Deployment

### AWS Linux Environment

1. **Update Environment Variables**:
   ```bash
   # In your .env file on production
   APP_TIMEZONE=Asia/Dubai
   ```

2. **Clear Configuration Cache**:
   ```bash
   php artisan config:clear
   php artisan config:cache
   ```

3. **Update Cron Jobs** (if any):
   ```bash
   # Ensure any scheduled tasks account for Dubai timezone
   # Example crontab entry:
   0 9 * * * cd /path/to/your/app && TZ=Asia/Dubai php artisan schedule:run
   ```

4. **Database Considerations**:
   - Existing UTC timestamps will be automatically converted when displayed
   - New timestamps will be stored in UTC but displayed in Dubai time
   - No database migration needed

5. **Web Server Configuration**:
   ```bash
   # Optional: Set system timezone (if you have server access)
   sudo timedatectl set-timezone Asia/Dubai
   ```

## Best Practices

1. **Always Use Helper Functions**: Don't format dates manually
2. **Consistent Components**: Use the `<x-dubai-date>` component when possible
3. **Test Timezone Conversion**: Verify dates display correctly
4. **Document Custom Formats**: If using unique formats, document them
5. **API Responses**: Consider timezone in API responses for mobile apps

## Testing

Test the implementation by:

1. Creating new records and verifying timestamps
2. Checking existing records display correctly
3. Testing JavaScript dynamic updates
4. Verifying relative time calculations
5. Testing across different browsers

## Troubleshooting

### Common Issues

1. **JavaScript timezone errors**: Ensure `dubai-date.js` is loaded
2. **Helper function not found**: Check `DateHelper.php` is loaded in `AppServiceProvider`
3. **Inconsistent formatting**: Use standardized helper functions
4. **Missing timezone indicator**: Use `showTimezone` parameter in components

### Debug Commands

```bash
# Check current timezone setting
php artisan tinker
>>> config('app.timezone')
>>> Carbon\Carbon::now()->timezone

# Test helper functions
>>> formatDubaiDate(now())
>>> nowDubai()
```

## Future Enhancements

Consider implementing:
- User-selectable timezones
- Automatic timezone detection
- Database timezone columns
- Enhanced mobile timezone handling
- Timezone-aware date pickers

---

This implementation ensures all dates and times across MaxMed are displayed consistently in Dubai timezone, improving user experience for local operations. 