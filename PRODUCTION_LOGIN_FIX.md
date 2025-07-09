# Production Login Fix Guide

## Issue Identified
The server error is caused by the admin dashboard trying to access database tables that don't exist or have incorrect names. The error shows:
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'maxmed.quotations' doesn't exist
```

## Root Cause
1. The `SupplierQuotation` model was not explicitly defining its table name
2. The dashboard controller was trying to access tables that might not exist in production
3. Missing proper error handling for database table access

## Fixes Applied

### 1. Fixed SupplierQuotation Model
- Added explicit table name definition: `protected $table = 'supplier_quotations';`
- Updated the relationship in SupplierInquiry model to specify foreign key

### 2. Temporarily Disabled Dashboard Stats
- Modified DashboardController to return empty stats to prevent immediate errors
- This allows login to work while the underlying issues are resolved

### 3. Created Fix Scripts
- `fix-production-issues.sh` (Linux/Mac)
- `fix-production-issues.ps1` (Windows)

## Immediate Production Fix Steps

### Step 1: Upload the Fixed Files
Upload these updated files to production:
- `app/Models/SupplierQuotation.php`
- `app/Models/SupplierInquiry.php`
- `app/Http/Controllers/Admin/DashboardController.php`

### Step 2: Run the Fix Script
```bash
# For Linux/Mac:
chmod +x fix-production-issues.sh
./fix-production-issues.sh

# For Windows:
.\fix-production-issues.ps1
```

### Step 3: Manual Commands (if script fails)
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Run migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Test Login
1. Try to login with admin credentials
2. Check if the dashboard loads without errors
3. Verify that registration works

## Long-term Fixes Needed

### 1. Restore Dashboard Stats
Once the immediate issue is resolved, uncomment the dashboard stats code in `DashboardController.php`

### 2. Verify All Tables Exist
Run this command to check what tables exist:
```bash
php artisan tinker --execute="echo 'Available tables:'; foreach(DB::select('SHOW TABLES') as \$table) { echo \$table->Tables_in_maxmed . PHP_EOL; }"
```

### 3. Check Missing Tables
If any tables are missing, run:
```bash
php artisan migrate:status
php artisan migrate --force
```

## Verification Steps

### 1. Check Error Logs
```bash
tail -f storage/logs/laravel.log
```

### 2. Test Authentication
- Try logging in with admin user
- Try registering a new user
- Check if password reset works

### 3. Check Dashboard
- Verify admin dashboard loads
- Check if stats are displayed (should be 0 for now)

## Rollback Plan
If issues persist:
1. Restore original files from backup
2. Check database connection settings
3. Verify all required tables exist
4. Check file permissions on storage/logs

## Notes
- The temporary dashboard fix returns all stats as 0
- This prevents server errors while allowing login to work
- Once the table issues are resolved, the original stats code can be restored 