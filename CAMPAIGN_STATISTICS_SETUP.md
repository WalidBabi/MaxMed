# Campaign Statistics Implementation

Your campaign system now has functional statistics tracking! Here's what has been implemented and how to test it.

## What Was Implemented

### 1. Email Tracking Controller (`app/Http/Controllers/EmailTrackingController.php`)
- **Open Tracking**: Tracks when emails are opened via invisible 1x1 pixel
- **Click Tracking**: Tracks when links in emails are clicked
- **Enhanced Unsubscribe**: Tracks unsubscribes per campaign

### 2. Email Tracking Service (`app/Services/EmailTrackingService.php`)
- Generates tracking URLs for opens and clicks
- Processes HTML content to add tracking pixels and make links trackable
- Handles unsubscribe URL generation with campaign context

### 3. Tracking Routes (added to `routes/web.php`)
```php
// Email Tracking Routes (public, no authentication required)
Route::get('/email/track/open/{trackingId}', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
Route::get('/email/track/click/{trackingId}', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');
Route::get('/email/unsubscribe/{token}', [EmailTrackingController::class, 'trackUnsubscribe'])->name('email.track.unsubscribe');
```

### 4. Test Command (`app/Console/Commands/TestCampaignStatistics.php`)
- Creates test data for campaign statistics
- Tests campaign sending and statistics tracking

## How It Works

### Email Open Tracking
1. When an email is sent, a tracking pixel is added: `<img src="/email/track/open/{trackingId}" width="1" height="1" style="display:none;" />`
2. When the recipient opens the email, the pixel loads and triggers the tracking endpoint
3. The system records the open time and updates campaign statistics

### Click Tracking
1. All links in email HTML are replaced with tracking URLs
2. When clicked, the user is redirected through `/email/track/click/{trackingId}`
3. The system records the click and redirects to the original URL
4. Campaign statistics are updated automatically

### Statistics Calculation
The Campaign model automatically calculates:
- **Recipients**: Total contacts in campaign
- **Sent**: Successfully sent emails  
- **Delivered**: Emails that reached recipient's inbox
- **Opens**: Unique opens (with open rate %)
- **Clicks**: Unique clicks (with click rate %)
- **Bounced**: Failed deliveries (with bounce rate %)
- **Unsubscribed**: Campaign-specific unsubscribes (with unsubscribe rate %)

## Testing the Implementation

### Step 1: Create Test Data
```bash
php artisan campaign:test-stats --create-test-data
```

This creates:
- A test marketing contact (test@example.com)
- A test campaign with HTML content including links
- Associates the contact with the campaign

### Step 2: Send Test Campaign
```bash
php artisan campaign:test-stats --campaign-id=YOUR_CAMPAIGN_ID
```

### Step 3: Process Queue
```bash
php artisan queue:work
```

### Step 4: Test Tracking
1. **Open Tracking**: Visit the generated tracking pixel URL
2. **Click Tracking**: Click any link in the email
3. **Unsubscribe**: Use the unsubscribe link

### Step 5: View Statistics
Check your campaign view or run the test command again to see updated statistics.

## Production Deployment

### For AWS Linux Production Environment

1. **Update Environment Variables**
```bash
# Add to your .env file
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

2. **Install Redis (if not already installed)**
```bash
sudo yum update -y
sudo yum install redis -y
sudo systemctl start redis
sudo systemctl enable redis
```

3. **Set up Queue Worker as Systemd Service**
Create `/etc/systemd/system/laravel-worker.service`:
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /path/to/your/app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always

[Install]
WantedBy=multi-user.target
```

4. **Enable and Start Service**
```bash
sudo systemctl daemon-reload
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

5. **Configure Cron for Statistics Updates**
Add to crontab:
```bash
# Update campaign statistics every 5 minutes
*/5 * * * * cd /path/to/your/app && php artisan campaign:update-stats
```

## Database Schema

The tracking uses existing tables:
- `campaigns` - stores campaign statistics
- `campaign_contacts` - tracks individual contact interactions
- `email_logs` - detailed email tracking logs
- `marketing_contacts` - contact management

## Webhook Integration (Optional)

For production email providers (SendGrid, Mailgun, etc.), implement webhook endpoints:

```php
// In routes/web.php
Route::post('/webhooks/email/sendgrid', [EmailTrackingController::class, 'handleSendgridWebhook']);
Route::post('/webhooks/email/mailgun', [EmailTrackingController::class, 'handleMailgunWebhook']);
```

This allows real-time delivery status updates instead of auto-marking as delivered.

## Monitoring

Monitor campaign performance through:
1. **Campaign Dashboard**: Real-time statistics display
2. **Analytics Reports**: Detailed performance metrics
3. **Email Logs**: Individual email tracking details
4. **Queue Monitoring**: Ensure emails are being processed

Your campaign statistics are now fully functional and ready for production use! 