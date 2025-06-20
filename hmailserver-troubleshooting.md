# 🔧 hMailServer Troubleshooting Guide

## Current Configuration Status
✅ **Laravel Mail Settings**: Correctly configured  
❓ **hMailServer**: Needs verification

## Step 1: Verify hMailServer is Running

1. **Check Windows Services**:
   - Press `Win + R`, type `services.msc`
   - Look for "hMailServer" service
   - Status should be "Running"
   - If stopped, right-click → Start

2. **Check hMailServer Logs**:
   - Open hMail Administrator
   - Go to `Utilities → Logging → View Current Log`
   - Look for errors when trying to send emails

## Step 2: Verify Domain and Account Configuration

### Domain Configuration:
1. Open hMail Administrator
2. Go to `Domains` → `localhost.com`
3. Verify:
   - ✅ Domain exists and is enabled
   - ✅ Domain name is exactly `localhost.com`

### Account Configuration:
1. Go to `Domains` → `localhost.com` → `Accounts`
2. Verify account `wbabi@localhost.com`:
   - ✅ Account exists
   - ✅ Password is `09888`
   - ✅ Account is active/enabled
   - ✅ Max size is not 0 (should be like 100 MB)

## Step 3: Check IP Ranges (Most Common Issue)

1. In hMail Administrator, go to `Settings` → `Protocols` → `SMTP` → `IP Ranges`
2. You should have an entry for `127.0.0.1` or `My Computer`:
   - **Lower IP**: 127.0.0.1
   - **Upper IP**: 127.0.0.1  
   - **Priority**: 15
   - ✅ **Allow connections to SMTP**: Checked
   - ✅ **Allow SMTP deliveries**: Checked
   - ✅ **Allow SMTP relaying**: Checked
   - ❌ **Require SMTP authentication**: UNCHECKED (for localhost)

## Step 4: SMTP Settings Verification

1. Go to `Settings` → `Protocols` → `SMTP`
2. Verify these settings:
   - **SMTP port**: 25 (enabled)
   - **Max connections**: 20 or higher
   - **Max delivery attempts**: 4
   - **Minutes between delivery attempts**: 60

## Step 5: Test Email Delivery

### A. Test from hMailServer directly:
1. In hMail Administrator
2. Go to `Utilities` → `Send Test Email`
3. Send from `wbabi@localhost.com` to `wbabi@localhost.com`

### B. Test from Laravel:
```bash
php artisan test:mail
```

### C. Check if emails are in queue:
1. In hMail Administrator
2. Go to `Status` → `SMTP Queue`
3. Look for stuck emails

## Step 6: Common Solutions

### If emails are not being delivered:

**Solution 1: Reset IP Ranges**
```
1. Delete all IP ranges
2. Add new range:
   - Lower: 127.0.0.1
   - Upper: 127.0.0.1
   - Priority: 15
   - Allow all SMTP options EXCEPT authentication
```

**Solution 2: Disable Authentication for Localhost**
```
1. Settings → Protocols → SMTP → IP Ranges
2. Edit localhost range
3. UNCHECK "Require SMTP authentication"
```

**Solution 3: Check Windows Firewall**
```
1. Windows Defender Firewall
2. Allow hMailServer through firewall
3. Or temporarily disable firewall to test
```

**Solution 4: Restart hMailServer**
```
1. Windows Services → hMailServer → Restart
2. Or restart computer
```

## Step 7: Alternative Quick Test

If hMailServer is being problematic, you can temporarily switch to file-based mail for testing:

Update your `.env`:
```env
MAIL_MAILER=log
```

This will save emails to `storage/logs/laravel.log` instead of sending them.

## Step 8: Production Considerations

When you deploy to AWS Linux production, you'll need different mail settings:

```env
# Production (AWS Linux)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@maxmedme.com"
MAIL_FROM_NAME="MaxMed UAE"
MAIL_ADMIN_EMAIL="admin@maxmedme.com"
```

## Need More Help?

Run these diagnostic commands and share the output:

```bash
# Check current config
php artisan config:show mail

# Test mail sending
php artisan test:mail

# Check logs
tail -n 50 storage/logs/laravel.log
``` 