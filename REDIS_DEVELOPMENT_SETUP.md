# Redis Development Setup for MaxMed Laravel Project

## Overview
This guide will help you set up Redis for caching and queue processing in your MaxMed Laravel application during development.

## 1. Install Redis Server

### Option A: Manual Installation (Recommended)
1. Download Redis for Windows from: https://github.com/tporadowski/redis/releases
2. Download the latest `Redis-x64-*.msi` file
3. Run the installer and follow the setup wizard
4. Make sure to check "Add Redis to PATH" during installation
5. Redis will automatically start as a Windows service

### Option B: Using Docker
If you prefer Docker:
```bash
docker run -d --name redis-dev -p 6379:6379 redis:latest
```

### Option C: Using WSL2 (Windows Subsystem for Linux)
```bash
# In WSL2 terminal
sudo apt update
sudo apt install redis-server
sudo service redis-server start
```

## 2. Install PHP Redis Client
Predis has been installed via Composer. This is a pure PHP implementation that doesn't require PHP extensions.

## 3. Environment Configuration

Add these Redis settings to your `.env` file:

```env
# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_CLIENT=predis

# Cache Configuration
CACHE_STORE=redis

# Queue Configuration (optional - you can keep database for development)
QUEUE_CONNECTION=redis
REDIS_QUEUE_CONNECTION=default
REDIS_QUEUE=default
REDIS_QUEUE_RETRY_AFTER=90

# Session Configuration (optional)
SESSION_DRIVER=redis
```

## 4. Test Redis Connection

Create a test artisan command or use tinker:
```bash
php artisan tinker
```

Then test:
```php
// Test basic Redis connection
Redis::set('test_key', 'Hello Redis!');
echo Redis::get('test_key');

// Test cache
Cache::put('test_cache', 'Cache working!', 60);
echo Cache::get('test_cache');
```

## 5. Configuration Files Update

Your configuration files are already set up for Redis:
- `config/cache.php` - Redis cache store configured
- `config/queue.php` - Redis queue connection configured
- `config/database.php` - Redis connections configured

## 6. Using Redis in Development

### For Caching:
```php
// Store cache
Cache::put('products_count', Product::count(), 3600);

// Retrieve cache
$count = Cache::get('products_count');

// Cache with tags (Redis supports tags)
Cache::tags(['products', 'categories'])->put('product_list', $products, 3600);
```

### For Queues:
```php
// Dispatch jobs to Redis queue
ProcessEmailCampaign::dispatch($campaign)->onQueue('campaigns');
```

### For Sessions:
Redis will automatically handle sessions if `SESSION_DRIVER=redis` is set.

## 7. Start Redis Queue Worker (if using Redis for queues)

```bash
# Process all queues
php artisan queue:work redis

# Process specific queue
php artisan queue:work redis --queue=campaigns

# Process with specific memory and timeout limits
php artisan queue:work redis --memory=512 --timeout=300
```

## 8. Monitoring Redis

### Check Redis Status:
```bash
redis-cli ping
# Should return: PONG
```

### Monitor Redis Commands:
```bash
redis-cli monitor
```

### Check Redis Info:
```bash
redis-cli info
```

### View All Keys:
```bash
redis-cli keys "*"
```

### Clear Cache:
```bash
php artisan cache:clear
# or
redis-cli flushdb
```

## 9. Production Considerations

### For AWS Linux Production:
1. Install Redis on your EC2 instance:
```bash
sudo yum update -y
sudo yum install redis -y
sudo systemctl start redis
sudo systemctl enable redis
```

2. Configure Redis for production:
```bash
sudo nano /etc/redis.conf
```

3. Set up Redis security:
- Change default port
- Set password authentication
- Configure firewall rules
- Enable persistence if needed

4. Update production `.env`:
```env
REDIS_HOST=your-production-redis-host
REDIS_PASSWORD=your-secure-password
REDIS_PORT=your-custom-port
```

## 10. Troubleshooting

### Common Issues:

1. **Connection Refused:**
   - Ensure Redis service is running
   - Check firewall settings
   - Verify host and port settings

2. **Memory Issues:**
   - Configure Redis maxmemory
   - Set eviction policies
   - Monitor memory usage

3. **Performance:**
   - Use Redis for appropriate use cases
   - Implement proper cache invalidation
   - Monitor key expiration

### Debug Commands:
```bash
# Check Laravel Redis connection
php artisan tinker
Redis::connection()->ping();

# Check cache configuration
php artisan config:cache
php artisan config:clear
```

## Benefits of Using Redis

1. **Performance:** Much faster than database/file caching
2. **Advanced Data Structures:** Lists, sets, sorted sets, hashes
3. **Atomic Operations:** Thread-safe operations
4. **Pub/Sub:** Real-time messaging capabilities
5. **Persistence:** Data can survive server restarts
6. **Scalability:** Easy to scale horizontally

## Next Steps

1. Install Redis server using one of the methods above
2. Update your `.env` file with Redis configuration
3. Test the connection using the provided commands
4. Start using Redis for caching in your application
5. Consider migrating queues to Redis for better performance

This setup will significantly improve your application's performance and provide a robust caching solution for development and production. 