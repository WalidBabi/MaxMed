# Production File Upload Size Fix

## Issue Description
The production server is returning a "413 Content Too Large" error when uploading company profile documents. The error shows:
- POST Content-Length: 8,663,595 bytes (8.3MB)
- Server limit: 8,388,608 bytes (8MB)

## Root Cause
The production server has lower PHP upload limits than the development environment, causing large PDF files (especially company profiles) to be rejected.

## Solution

### 1. Update PHP Configuration

#### For Apache/Nginx with PHP-FPM:
Create or update your `php.ini` file with these settings:

```ini
; File upload settings
upload_max_filesize = 50M
post_max_size = 50M
memory_limit = 512M
max_execution_time = 600
max_input_time = 600
```

#### For AWS Linux/EC2:
1. Find your PHP configuration file:
   ```bash
   php --ini
   ```

2. Edit the main php.ini file (usually `/etc/php/8.x/fpm/php.ini` or `/etc/php/8.x/apache2/php.ini`):
   ```bash
   sudo nano /etc/php/8.x/fpm/php.ini
   ```

3. Update these values:
   ```ini
   upload_max_filesize = 50M
   post_max_size = 50M
   memory_limit = 512M
   max_execution_time = 600
   max_input_time = 600
   ```

4. Restart PHP-FPM and web server:
   ```bash
   sudo systemctl restart php8.x-fpm
   sudo systemctl restart apache2  # or nginx
   ```

#### For Nginx (if using):
Also update your Nginx configuration to handle larger uploads:

```nginx
# In your server block or http block
client_max_body_size 50M;
```

### 2. Update Web Server Configuration

#### Apache (.htaccess or virtual host):
```apache
php_value upload_max_filesize 50M
php_value post_max_size 50M
php_value memory_limit 512M
php_value max_execution_time 600
php_value max_input_time 600
```

#### Nginx:
```nginx
client_max_body_size 50M;
```

### 3. Verify Changes

After making changes, verify the new limits:

```bash
# Create a PHP info file temporarily
echo "<?php phpinfo(); ?>" > /var/www/html/phpinfo.php

# Check the values in your browser or via command line
php -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"
php -r "echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;"
php -r "echo 'memory_limit: ' . ini_get('memory_limit') . PHP_EOL;"
```

### 4. Application Changes Already Made

The following changes have been implemented in the codebase:

1. **Updated validation rules** in `OnboardingController.php`:
   - Trade License: 20MB (was 5MB)
   - Tax Certificate: 20MB (was 5MB)
   - Company Profile: 40MB (was 10MB)
   - Certifications: 20MB each (was 5MB)

2. **Enhanced UI** in `documents.blade.php`:
   - Shows previously uploaded documents
   - Updated file size limits in UI text
   - Conditional required fields (not required if already uploaded)
   - Better user experience with document status indicators

3. **Updated development configuration** in `docker/php/custom.ini`:
   - Increased limits to 50M for upload_max_filesize and post_max_size
   - Increased memory_limit to 512M
   - Increased execution time to 600 seconds

### 5. Testing

After implementing the changes:

1. Test with a large company profile PDF (8-10MB)
2. Verify all document types can be uploaded
3. Check that existing documents are displayed correctly
4. Ensure the onboarding flow works end-to-end

### 6. Security Considerations

- The increased limits are necessary for business documents
- File type validation is still enforced (PDF, JPG, PNG only)
- Files are stored securely in the `supplier_documents` disk
- Consider implementing virus scanning for uploaded files in production

### 7. Monitoring

Monitor server resources after implementing these changes:
- Memory usage
- Disk space
- Upload success rates
- Server response times

## Rollback Plan

If issues occur, you can temporarily reduce the limits:
```ini
upload_max_filesize = 20M
post_max_size = 20M
memory_limit = 256M
```

But this may cause the same upload issues for large company profiles. 