#!/bin/bash

# Safe Production Deployment Script
# This script ensures safe deployment without data loss

set -e  # Exit on any error

echo "=== MAXMED SAFE PRODUCTION DEPLOYMENT ==="
echo "Starting deployment at $(date)"

# Configuration
BACKUP_DIR="/backup/maxmed"
DB_NAME="${DB_DATABASE:-maxmed_production}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

echo "=== STEP 1: PRE-DEPLOYMENT BACKUP ==="
echo "Creating database backup..."
if ! mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_NAME > "$BACKUP_DIR/pre_deploy_$TIMESTAMP.sql"; then
    echo "❌ BACKUP FAILED - ABORTING DEPLOYMENT"
    exit 1
fi
echo "✅ Backup created: pre_deploy_$TIMESTAMP.sql"

echo "=== STEP 2: CODE UPDATE ==="
echo "Pulling latest code..."
git fetch origin
git pull origin main

echo "=== STEP 3: DEPENDENCY UPDATE ==="
echo "Installing/updating dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "=== STEP 4: MIGRATION CHECK ==="
echo "Checking for pending migrations..."
php artisan migrate:status

echo "⚠️  MIGRATION SAFETY CHECK"
echo "The following migrations will be run:"
php artisan migrate --pretend

read -p "Do these migrations look safe? (y/N): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Deployment cancelled by user"
    exit 1
fi

echo "=== STEP 5: SAFE MIGRATION ==="
echo "Running migrations..."
php artisan migrate --force

echo "=== STEP 6: CACHE MANAGEMENT ==="
echo "Clearing old caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

echo "Rebuilding caches for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo "=== STEP 7: VERIFICATION ==="
echo "Testing database connection..."
php artisan tinker --execute="
try {
    DB::connection()->getPdo();
    echo 'Database connection: OK\n';
    echo 'Deliveries count: ' . DB::table('deliveries')->count() . '\n';
} catch (Exception \$e) {
    echo 'Database error: ' . \$e->getMessage() . '\n';
    exit(1);
}
"

echo "=== STEP 8: POST-DEPLOYMENT BACKUP ==="
echo "Creating post-deployment backup..."
mysqldump -u $DB_USERNAME -p$DB_PASSWORD $DB_NAME > "$BACKUP_DIR/post_deploy_$TIMESTAMP.sql"
echo "✅ Post-deployment backup created"

echo "=== DEPLOYMENT COMPLETE ==="
echo "Completed at $(date)"
echo "Backups stored in: $BACKUP_DIR"
echo ""
echo "📋 DEPLOYMENT SUMMARY:"
echo "   - Pre-deployment backup: pre_deploy_$TIMESTAMP.sql"
echo "   - Post-deployment backup: post_deploy_$TIMESTAMP.sql"
echo "   - Code updated from: $(git log -1 --format='%h - %s')"
echo ""
echo "🔍 Next steps:"
echo "   1. Test critical functionality"
echo "   2. Monitor logs: tail -f storage/logs/laravel.log"
echo "   3. Verify deliveries data is intact"
