# Supplier Quotation PDF Display Feature

## Overview
This feature allows administrators to view PDF files and other attachments submitted by suppliers in their quotations through the admin interface.

## Changes Made

### 1. Updated SupplierQuotation Model
- Added `attachments` to the `$fillable` array
- Added `attachments` to the `$casts` array as `array`

### 2. Updated Admin Views

#### Quotation Show View (`resources/views/admin/inquiry-quotations/quotation-show.blade.php`)
- Added a new section "Supplier Submitted Attachments" that displays:
  - File name and icon for each attachment
  - "Open" button to view files in new tab
  - PDF preview using iframe for PDF files
  - Placeholder for non-PDF files with download option
- Supports multiple attachments per quotation
- Responsive grid layout (1 column on mobile, 2 columns on desktop)

#### Inquiry Show View (`resources/views/admin/inquiry-quotations/show.blade.php`)
- Added "Attachments" column to the quotations table
- Shows file count with document icon for quotations with attachments
- Displays "No attachments" for quotations without files

## Features

### PDF Display
- PDF files are displayed inline using iframe
- 400px height for optimal viewing
- Responsive width (100% of container)

### File Management
- Supports multiple file types (PDF, JPG, PNG, DOC, DOCX)
- Files are stored in `storage/app/public/supplier_quotations/`
- Original file names are preserved
- Direct download links for all file types

### User Experience
- Clean, modern interface consistent with existing admin design
- File type indicators with appropriate icons
- Responsive design for mobile and desktop
- Loading states and error handling

## File Storage
- Attachments are stored in the `supplier_quotations` table as JSON
- Each attachment contains:
  - `name`: Original file name
  - `path`: Storage path relative to public storage

## Production Deployment Notes

### For AWS Linux Production Environment:

1. **Storage Configuration**
   ```bash
   # Ensure storage link exists
   php artisan storage:link
   
   # Set proper permissions
   sudo chown -R www-data:www-data storage/
   sudo chmod -R 755 storage/
   ```

2. **File Upload Limits**
   - Update `php.ini` settings:
     ```ini
     upload_max_filesize = 20M
     post_max_size = 20M
     max_execution_time = 300
     memory_limit = 256M
     ```

3. **Nginx Configuration** (if using Nginx)
   ```nginx
   # Add to server block
   location /storage {
       alias /path/to/your/app/storage/app/public;
       expires 1y;
       add_header Cache-Control "public, immutable";
   }
   ```

4. **Security Considerations**
   - Files are stored in public storage for easy access
   - Consider implementing file type validation on server side
   - Monitor storage usage for large file uploads
   - Implement file cleanup for rejected/expired quotations

## Testing
- Test with various file types (PDF, images, documents)
- Verify PDF preview functionality
- Test responsive design on different screen sizes
- Ensure file downloads work correctly
- Test with multiple attachments per quotation

## Future Enhancements
- File preview for images (thumbnails)
- Document preview for Word/Excel files
- File size display
- Upload progress indicators
- Bulk download functionality
- File compression for large uploads 