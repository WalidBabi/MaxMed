#!/bin/bash
# Troubleshooting script for push notifications

echo "🔍 Troubleshooting Push Notifications"
echo "======================================"
echo ""

cd ~/MaxMed

echo "1️⃣ Checking database tables..."
if php artisan tinker --execute="echo Schema::hasTable('push_subscriptions') ? '✅ push_subscriptions exists' : '❌ push_subscriptions missing'; echo PHP_EOL;" > /dev/null 2>&1; then
    echo "   ✅ Can access database"
else
    echo "   ❌ Cannot access database"
fi

echo ""
echo "2️⃣ Checking migrations..."
php artisan migrate:status 2>/dev/null | grep push || echo "   ⚠️  No push migrations found"

echo ""
echo "3️⃣ Checking middleware file..."
if [ -f "app/Http/Middleware/AuthenticatePushToken.php" ]; then
    echo "   ✅ AuthenticatePushToken middleware exists"
else
    echo "   ❌ AuthenticatePushToken middleware missing"
fi

echo ""
echo "4️⃣ Checking Kernel.php registration..."
if grep -q "push.token.*AuthenticatePushToken" app/Http/Kernel.php; then
    echo "   ✅ push.token middleware registered in Kernel.php"
else
    echo "   ❌ push.token middleware NOT registered"
    echo "   This is likely the issue! Check line 99 of app/Http/Kernel.php"
fi

echo ""
echo "5️⃣ Checking routes..."
if grep -q "push/subscribe" routes/web.php; then
    echo "   ✅ Push routes exist"
else
    echo "   ❌ Push routes missing"
fi

echo ""
echo "6️⃣ Checking service worker..."
if [ -f "public/service-worker.js" ]; then
    echo "   ✅ service-worker.js exists"
else
    echo "   ❌ service-worker.js missing"
fi

echo ""
echo "7️⃣ Checking subscriptions count..."
php artisan tinker --execute="
use Illuminate\Support\Facades\DB;
\$count = DB::table('push_subscriptions')->count();
echo '   📊 Total subscriptions: ' . \$count . PHP_EOL;
if (\$count > 0) {
    echo '   ✅ Subscriptions found in database' . PHP_EOL;
} else {
    echo '   ⚠️  No subscriptions in database yet' . PHP_EOL;
}
" 2>/dev/null

echo ""
echo "8️⃣ Checking VAPID keys..."
php artisan tinker --execute="
if (config('webpush.vapid.public_key')) {
    echo '   ✅ VAPID public key configured' . PHP_EOL;
} else {
    echo '   ❌ VAPID public key missing!' . PHP_EOL;
}
" 2>/dev/null

echo ""
echo "9️⃣ Testing web push routes..."
curl -s -o /dev/null -w "   Status: %{http_code}\n" https://maxmedme.com/push/public-key || echo "   ❌ Cannot reach /push/public-key"

echo ""
echo "🔟 Checking logs for errors..."
tail -n 50 storage/logs/laravel.log 2>/dev/null | grep -i "push\|auth" | tail -5 || echo "   ℹ️  No recent push errors in logs"

echo ""
echo "======================================"
echo "✅ Diagnostic complete!"
echo ""
echo "💡 Next steps:"
echo "   1. If push.token middleware not registered → Fix Kernel.php conflict"
echo "   2. If VAPID keys missing → Configure in .env"
echo "   3. If no subscriptions → Try subscribing from browser"
echo "   4. If can't access routes → Clear cache"
echo ""

