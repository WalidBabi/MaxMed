# SSL Certificate Fix Guide for MaxMed

## 🚨 Issue Summary

Your website `maxmedme.com` is currently only accessible via HTTP because the SSL certificate has expired. The certificate expired on **August 29, 2025**, and today is **August 30, 2025**.

### What Happened
- Your Let's Encrypt SSL certificate expired
- Browsers are blocking HTTPS access due to the expired certificate
- Your nginx configuration forces HTTPS redirects
- This creates a redirect loop, making the site inaccessible via HTTPS

### Current Status
- ✅ Website accessible via HTTP: `http://maxmedme.com`
- ❌ Website blocked via HTTPS: `https://maxmedme.com` (certificate expired)

## 🔧 Solution Overview

We need to:
1. **Temporarily allow HTTP access** while fixing the certificate
2. **Renew the expired SSL certificate** using Let's Encrypt
3. **Restore HTTPS redirects** once the certificate is valid
4. **Set up automatic renewal** to prevent future issues

## 📋 Files Created

### 1. `fix-ssl-certificate.sh` (Linux/Mac)
- Comprehensive bash script to fix the SSL certificate
- Handles Docker and system nginx configurations
- Sets up automatic renewal via cron

### 2. `fix-ssl-certificate.ps1` (Windows)
- PowerShell version of the fix script
- Handles Windows-specific configurations
- Sets up automatic renewal via Task Scheduler

### 3. `docker/nginx/conf.d/app-temp.conf`
- Temporary nginx configuration for HTTP-only access
- Removes HTTPS redirects temporarily
- Maintains security headers and performance optimizations

### 4. `ssl-certificate-renewal.sh`
- Simple certificate renewal script
- Basic renewal functionality

## 🚀 Quick Fix Instructions

### Option 1: Automated Fix (Recommended)

#### For Linux/Mac:
```bash
# Make the script executable
chmod +x fix-ssl-certificate.sh

# Run the fix script (requires sudo)
sudo ./fix-ssl-certificate.sh
```

#### For Windows:
```powershell
# Run PowerShell as Administrator
# Navigate to your project directory
.\fix-ssl-certificate.ps1
```

### Option 2: Manual Fix

#### Step 1: Apply Temporary Configuration
```bash
# Backup current configuration
cp docker/nginx/conf.d/app.conf docker/nginx/conf.d/app.conf.backup

# Apply temporary HTTP-only configuration
cp docker/nginx/conf.d/app-temp.conf docker/nginx/conf.d/app.conf

# Restart nginx
docker-compose restart nginx
```

#### Step 2: Renew SSL Certificate
```bash
# Stop nginx to free port 80
docker-compose stop nginx

# Renew the certificate
certbot renew --force-renewal --cert-name maxmedme.com

# If renewal fails, obtain a new certificate
certbot certonly --standalone -d maxmedme.com -d www.maxmedme.com --agree-tos --email admin@maxmedme.com --force-renewal
```

#### Step 3: Restore HTTPS Configuration
```bash
# Restore original configuration
cp docker/nginx/conf.d/app.conf.backup docker/nginx/conf.d/app.conf

# Test nginx configuration
docker-compose exec nginx nginx -t

# Restart nginx with HTTPS
docker-compose up -d nginx
```

## 🔍 Verification Steps

After running the fix:

1. **Test HTTPS access**: Visit `https://maxmedme.com`
2. **Check certificate**: `certbot certificates`
3. **Verify redirects**: HTTP should redirect to HTTPS
4. **Test all pages**: Ensure no SSL errors

## 🛠️ Troubleshooting

### Certificate Renewal Fails
```bash
# Check certbot logs
journalctl -u certbot

# Manual certificate check
openssl x509 -in /etc/letsencrypt/live/maxmedme.com/cert.pem -noout -dates

# Force new certificate
certbot certonly --standalone -d maxmedme.com -d www.maxmedme.com --agree-tos --email admin@maxmedme.com --force-renewal
```

### Nginx Configuration Issues
```bash
# Test nginx configuration
docker-compose exec nginx nginx -t

# Check nginx logs
docker-compose logs nginx

# Restart nginx
docker-compose restart nginx
```

### Docker Issues
```bash
# Check Docker status
docker-compose ps

# Rebuild containers if needed
docker-compose down
docker-compose up -d --build
```

## 🔄 Automatic Renewal Setup

### Linux/Mac (Cron)
The script automatically creates a cron job:
```bash
# Check if cron job was created
crontab -l | grep ssl-renewal

# Manual cron job creation
echo "0 12 * * * root /usr/bin/certbot renew --quiet --deploy-hook 'systemctl reload nginx || docker-compose restart nginx'" | sudo tee /etc/cron.d/ssl-renewal
```

### Windows (Task Scheduler)
The PowerShell script creates a scheduled task:
- Task Name: `MaxMed-SSL-Renewal`
- Schedule: Daily at 12:00 PM
- Command: `certbot renew --quiet`

## 📊 Monitoring

### Check Certificate Status
```bash
# List all certificates
certbot certificates

# Check specific certificate
openssl x509 -in /etc/letsencrypt/live/maxmedme.com/cert.pem -noout -text | grep -A 2 "Validity"
```

### Monitor Renewal Logs
```bash
# Linux/Mac
journalctl -u certbot -f

# Windows
Get-EventLog -LogName Application -Source "certbot" -Newest 10
```

## 🚨 Prevention

To prevent future SSL certificate issues:

1. **Automatic Renewal**: Ensure cron jobs or scheduled tasks are active
2. **Monitoring**: Set up alerts for certificate expiration
3. **Backup**: Keep backup configurations
4. **Testing**: Regularly test certificate renewal process

## 📞 Support

If you encounter issues:

1. Check the logs mentioned in troubleshooting
2. Verify DNS settings for `maxmedme.com`
3. Ensure port 80 is accessible for Let's Encrypt validation
4. Contact your hosting provider if using managed hosting

## 🔗 Useful Commands

```bash
# Check certificate expiration
openssl x509 -in /etc/letsencrypt/live/maxmedme.com/cert.pem -noout -dates

# Test SSL connection
openssl s_client -connect maxmedme.com:443 -servername maxmedme.com

# Check nginx status
docker-compose ps nginx

# View nginx configuration
docker-compose exec nginx cat /etc/nginx/conf.d/app.conf

# Check Let's Encrypt logs
tail -f /var/log/letsencrypt/letsencrypt.log
```

---

**Last Updated**: August 30, 2025  
**Status**: SSL Certificate Expired - Fix Required  
**Priority**: High - Affects website accessibility
