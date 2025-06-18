# Profile Photo Upload Debug Guide

## Issue: Save Changes Button Not Submitting/Saving Photo

### Changes Made:

1. **Enhanced ProfileController** with debugging logs
2. **Updated JavaScript** with better validation and debugging
3. **Added visual feedback** for file selection

### Debugging Steps:

#### 1. Check Browser Console
- Open Developer Tools (F12)
- Go to Console tab
- Submit the form and look for JavaScript errors or logs

#### 2. Check Network Tab
- Open Developer Tools → Network tab
- Submit the form
- Look for the PATCH request to `/profile`
- Check if the request contains the file data

#### 3. Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

#### 4. Manual Testing Checklist:
- [ ] Form has `enctype="multipart/form-data"`
- [ ] File input has `name="profile_photo"`
- [ ] CSRF token is present
- [ ] File size is under 2MB
- [ ] File type is an image (jpg, png, gif, webp)

#### 5. Common Issues and Solutions:

**Issue: Form not submitting**
- Check if JavaScript errors are preventing submission
- Verify CSRF token is valid
- Check if max_file_uploads in php.ini is sufficient

**Issue: File not uploading**
- Check php.ini settings:
  - `upload_max_filesize` (should be ≥ 2M)
  - `post_max_size` (should be ≥ 2M)
  - `max_file_uploads` (should be ≥ 1)

**Issue: Validation errors**
- Check ProfileUpdateRequest validation rules
- Ensure file meets validation criteria

**Issue: Storage errors**
- Verify `storage/app/public` directory exists and is writable
- Check if `public/storage` symlink exists (`php artisan storage:link`)

### Testing Commands:

```bash
# Check PHP settings
php -i | grep -E "(upload_max_filesize|post_max_size|max_file_uploads)"

# Check storage link
ls -la public/storage

# Test with Artisan Tinker
php artisan tinker
>>> use Illuminate\Http\UploadedFile;
>>> $file = UploadedFile::fake()->image('test.jpg');
>>> dd($file);
```

### Browser Testing:

1. Open profile edit page
2. Select a small image file (< 100KB)
3. Check debug info appears below file input
4. Submit form
5. Check console for form data logs
6. Check network tab for request details

### Files Modified:
- `resources/views/profile/edit.blade.php` - Enhanced form with debugging
- `app/Http/Controllers/ProfileController.php` - Added logging
- `tests/Feature/ProfilePhotoTest.php` - Comprehensive tests

### Next Steps if Still Not Working:

1. Check if the route exists: `php artisan route:list | grep profile`
2. Verify middleware isn't blocking the request
3. Test with a simple HTML form outside Laravel
4. Check web server error logs
5. Temporarily disable CSRF protection for testing

### Production Deployment Notes:

When deploying to AWS Linux:
- Ensure proper file permissions on storage directories
- Configure web server (Apache/Nginx) for file uploads
- Set appropriate PHP-FPM settings for file uploads
- Use proper storage disk configuration for production 