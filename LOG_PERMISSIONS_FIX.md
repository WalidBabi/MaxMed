# Log Permissions Fix

## Problem
The website was experiencing server errors because `laravel.log` files were being tracked in git, and in production, file permissions were preventing Laravel from writing to the log files.

## Solution Implemented

### 1. Removed Log Files from Git Tracking
- Removed `storage/logs/laravel.log`, `storage/logs/custom.log`, and `storage/logs/test.log` from git tracking
- Updated `.gitignore` to ensure all log files are properly ignored

### 2. Enhanced Logging Configuration
- Added permission settings to logging channels in `config/logging.php`
- Added a fallback channel that uses `stderr` if file logging fails
- Created a custom `LoggingServiceProvider` to handle permission issues gracefully

### 3. Created Fix Scripts
- `fix-log-permissions.php`: PHP script to fix permissions locally
- `deploy-fix-logs.sh`: Bash script for production deployment

## How to Fix in Production

### Option 1: Manual Fix
```bash
# Navigate to your Laravel application directory
cd /path/to/your/laravel/app

# Create logs directory with proper permissions
mkdir -p storage/logs
chmod 755 storage/logs

# Create log files with proper permissions
touch storage/logs/laravel.log
touch storage/logs/custom.log
touch storage/logs/test.log
touch storage/logs/production-debug.log
touch storage/logs/critical-errors.log

# Set proper permissions
chmod 664 storage/logs/*.log

# Set ownership (adjust user as needed)
chown www-data:www-data storage/logs/*.log

# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
```

### Option 2: Use the Deployment Script
```bash
# Make the script executable
chmod +x deploy-fix-logs.sh

# Edit the script to set the correct path
# Then run it
./deploy-fix-logs.sh
```

### Option 3: Use the PHP Script
```bash
php fix-log-permissions.php
```

### Option 4: Test Logging Functionality
```bash
php test-logging.php
```

## Prevention

### 1. Always Check .gitignore
Before committing, ensure that:
- `/storage/logs/` is in `.gitignore`
- `*.log` is in `.gitignore`
- No log files are tracked in git

### 2. Production Deployment Checklist
- [ ] Run permission fix script
- [ ] Clear Laravel caches
- [ ] Test logging functionality
- [ ] Verify web server user has write permissions

### 3. Monitoring
- Monitor log file permissions in production
- Set up alerts for logging failures
- Regularly check log file sizes and rotate if needed

## Verification

To verify the fix is working:

```bash
# Test logging functionality
php artisan tinker --execute="Log::info('Test log message');"

# Check if log file was created and is writable
ls -la storage/logs/
tail -f storage/logs/laravel.log
```

## Troubleshooting

### If logs still don't work:
1. Check web server user permissions
2. Verify storage/logs directory exists
3. Ensure log files are writable
4. Check SELinux/AppArmor restrictions
5. Review web server error logs

### Common Issues:
- **Permission Denied**: Run `chmod 664 storage/logs/*.log`
- **Directory Not Found**: Run `mkdir -p storage/logs`
- **Owner Issues**: Run `chown www-data:www-data storage/logs/*.log` (adjust user as needed) 