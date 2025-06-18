# XAMPP MySQL Shutdown Fix Guide

## Common Solutions for "MySQL shutdown unexpectedly"

### Solution 1: Check for Port Conflicts
1. Open XAMPP Control Panel as Administrator
2. Click "Config" next to MySQL → "my.ini"
3. Find the line with `port=3306`
4. Try changing it to `port=3307` or `port=3308`
5. Save and restart MySQL

### Solution 2: Delete MySQL Log Files (Most Common Fix)
1. Stop MySQL service in XAMPP
2. Navigate to: `C:\xampp\mysql\data\`
3. Look for files like:
   - `ib_logfile0`
   - `ib_logfile1` 
   - `ibdata1` (backup this first!)
4. Delete `ib_logfile0` and `ib_logfile1` (NOT ibdata1)
5. Start MySQL again

### Solution 3: Backup and Reset MySQL Data
**⚠️ This will delete all databases - backup first!**

1. Backup your databases:
   - Go to `C:\xampp\mysql\data\`
   - Copy your database folders (like `maxmed`, `phpmyadmin`, etc.)
2. Stop MySQL
3. Rename `C:\xampp\mysql\data\` to `C:\xampp\mysql\data_backup\`
4. Copy `C:\xampp\mysql\backup\` to `C:\xampp\mysql\data\`
5. Start MySQL
6. Restore your database from SQL backup

### Solution 4: Check MySQL Error Log
1. Go to `C:\xampp\mysql\data\`
2. Look for `.err` files (like `hostname.err`)
3. Open with notepad to see specific error

### Solution 5: Run as Administrator
1. Right-click XAMPP Control Panel
2. Select "Run as Administrator"
3. Try starting MySQL

### Solution 6: Reinstall MySQL Module
1. Stop all XAMPP services
2. Go to `C:\xampp\mysql\`
3. Rename `mysql` folder to `mysql_backup`
4. Download fresh XAMPP and extract only the mysql folder
5. Replace with new mysql folder
6. Copy your databases from backup

## Quick Fix Commands (Run in PowerShell as Admin):

```powershell
# Stop XAMPP MySQL service
net stop mysql

# Navigate to XAMPP MySQL data directory
cd C:\xampp\mysql\data\

# Delete problematic log files
del ib_logfile0
del ib_logfile1

# Start XAMPP MySQL service
net start mysql
```

## After Fixing MySQL:

1. Start Apache and MySQL in XAMPP
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Check if your databases are intact
4. Test the profile photo upload fix we implemented

## Prevention Tips:

1. Always stop XAMPP properly before shutting down Windows
2. Don't force-close XAMPP processes
3. Regular database backups
4. Run XAMPP as Administrator
5. Keep Windows Defender/Antivirus exceptions for XAMPP folder

## If Nothing Works:

1. Export all databases via phpMyAdmin
2. Uninstall XAMPP completely
3. Delete `C:\xampp` folder
4. Reinstall XAMPP
5. Import databases back

---

**Note:** After fixing MySQL, you can test the profile photo upload fix we implemented in the ProfileController! 