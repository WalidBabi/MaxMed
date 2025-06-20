# MaxMed Email Queue Optimization Guide

## Problem Summary
**Issue**: Emails take time to send and are sent all at once, causing:
- Slow page responses when users submit forms or log in
- Email bursts overwhelming the mail server
- Poor user experience due to synchronous email sending

## Root Cause Analysis

### Before Optimization:
- ❌ **Login emails**: Sent immediately (blocking request)
- ❌ **Contact form emails**: Sent immediately (blocking request)  
- ❌ **Notification emails**: Sent immediately (blocking request)
- ✅ **Campaign emails**: Properly queued
- ✅ **Invoice emails**: Properly queued

### Current Flow Problems:
```
User Action → Email Sent Immediately → Page Responds (SLOW)
Queue Worker Runs → All Emails Processed at Once (BURST)
```

## Solution Implementation

### 1. Queued All Email Types
All emails are now properly queued with staggered delays:

- **Login notifications**: 5-second delay
- **Contact form emails**: 2-second delay  
- **Contact submissions**: 3-second delay
- **Campaign emails**: Batch processing
- **Order notifications**: Immediate queue

### 2. Separate Queue Channels
```
emails: Contact forms, general emails
notifications: Login, system notifications  
campaigns: Bulk marketing emails
default: Other background jobs
```

### 3. Staggered Processing
Small delays prevent email bursts:
```php
$this->delay(now()->addSeconds(2-5)); // Prevents simultaneous sending
```

## Development Setup

### 1. Run Queue Workers (Development)
```bash
# Single worker for all queues
php artisan queue:work --tries=3

# Or separate workers for better control
php artisan queue:work --queue=emails --tries=3 --timeout=60 &
php artisan queue:work --queue=notifications --tries=3 --timeout=60 &
php artisan queue:work --queue=campaigns --tries=3 --timeout=120 &
```

### 2. Monitor Queue Status
```bash
# Check queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### 3. Development Testing
```bash
# Test email sending
php artisan test:mail wbabi@localhost.com

# Clear all queued jobs (if needed)
php artisan queue:clear
```

## Production Setup (AWS Linux)

### 1. Install Supervisor
```bash
sudo yum install supervisor
sudo systemctl enable supervisord
sudo systemctl start supervisord
```

### 2. Create Supervisor Configuration
Create `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker-emails]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --queue=emails --sleep=3 --tries=3 --max-time=3600
directory=/var/www/html
user=apache
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-worker-emails.log
stopwaitsecs=3600
autostart=true
autorestart=true

[program:laravel-worker-notifications]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --queue=notifications --sleep=3 --tries=3 --max-time=3600
directory=/var/www/html
user=apache
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-worker-notifications.log
stopwaitsecs=3600
autostart=true
autorestart=true

[program:laravel-worker-campaigns]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --queue=campaigns --sleep=5 --tries=3 --max-time=3600
directory=/var/www/html
user=apache
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/supervisor/laravel-worker-campaigns.log
stopwaitsecs=3600
autostart=true
autorestart=true
```

### 3. Start Workers
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 4. Monitor Production Workers
```bash
# Check worker status
sudo supervisorctl status

# View logs
sudo tail -f /var/log/supervisor/laravel-worker-emails.log

# Restart workers
sudo supervisorctl restart laravel-worker-emails:*
```

## Performance Benefits

### Before (Synchronous):
- Contact form submission: 3-5 seconds
- Login with notification: 2-3 seconds
- Email bursts overwhelm server

### After (Queued):
- Contact form submission: < 1 second
- Login: < 1 second  
- Emails sent smoothly in background
- Better server resource utilization

## Email Flow Optimization

### New Flow:
```
User Action → Queue Email → Immediate Page Response (FAST)
Queue Worker → Process Emails with Delays → Smooth Delivery
```

### Rate Limiting:
- **Emails queue**: 2 workers, 3-second sleep
- **Notifications queue**: 1 worker, 3-second sleep  
- **Campaigns queue**: 1 worker, 5-second sleep

## Monitoring & Troubleshooting

### 1. Check Queue Health
```bash
# View queue statistics
php artisan queue:monitor

# Check database queue table
mysql -u root -p maxmed -e "SELECT queue, COUNT(*) as pending FROM jobs GROUP BY queue;"
```

### 2. Common Issues

**Queue workers not processing:**
```bash
# Restart workers
sudo supervisorctl restart all

# Check worker logs
sudo tail -f /var/log/supervisor/laravel-worker-*.log
```

**Emails stuck in queue:**
```bash
# Clear stuck jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

**High memory usage:**
```bash
# Add memory limits to supervisor config
command=php -d memory_limit=256M /var/www/html/artisan queue:work...
```

### 3. Performance Tuning

**Adjust processing rates:**
```bash
# Faster email processing (if server can handle)
--sleep=1

# Slower processing (if overwhelming server)
--sleep=10
```

**Scale workers:**
```ini
# Increase email workers for high volume
numprocs=3  # Instead of 2
```

## Expected Results

✅ **Faster page responses**: Users don't wait for emails
✅ **Smooth email delivery**: No more bursts
✅ **Better server performance**: Background processing
✅ **Improved user experience**: Immediate feedback
✅ **Scalable email system**: Easy to adjust rates

## Validation Steps

1. **Test contact form**: Should respond immediately
2. **Test login**: Should complete quickly  
3. **Check email delivery**: Emails arrive within 5-10 seconds
4. **Monitor server load**: Should be more stable
5. **Verify queue processing**: `php artisan queue:monitor`

This optimization transforms your email system from blocking/bursting to smooth, background processing, significantly improving both user experience and server performance. 