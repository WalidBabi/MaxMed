# SSL Certificate Fix Script for MaxMed (PowerShell Version)
# This script will fix the expired SSL certificate issue

param(
    [switch]$SkipDocker,
    [string]$Email = "admin@maxmedme.com"
)

Write-Host "🔒 SSL Certificate Fix Script for MaxMed" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Function to print colored output
function Write-Status {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Blue
}

function Write-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Check if running as administrator
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole] "Administrator")) {
    Write-Error "This script must be run as Administrator"
    exit 1
}

Write-Status "Starting SSL certificate fix process..."

# Step 1: Backup current configuration
Write-Status "Backing up current nginx configuration..."
$backupName = "app.conf.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item "docker/nginx/conf.d/app.conf" "docker/nginx/conf.d/$backupName"

# Step 2: Apply temporary HTTP-only configuration
Write-Status "Applying temporary HTTP-only configuration..."
Copy-Item "docker/nginx/conf.d/app-temp.conf" "docker/nginx/conf.d/app.conf"

# Step 3: Restart nginx with temporary configuration
Write-Status "Restarting nginx with temporary configuration..."
if (-not $SkipDocker) {
    try {
        docker-compose restart nginx
        Write-Success "Docker nginx restarted successfully"
    }
    catch {
        Write-Warning "Docker restart failed, trying systemctl..."
        try {
            systemctl restart nginx
            Write-Success "System nginx restarted successfully"
        }
        catch {
            Write-Error "Failed to restart nginx"
        }
    }
}

Write-Success "Website is now accessible via HTTP while we fix the SSL certificate"

# Step 4: Check if certbot is installed
Write-Status "Checking certbot installation..."
try {
    $certbotVersion = certbot --version
    Write-Success "Certbot is installed: $certbotVersion"
}
catch {
    Write-Status "Certbot not found. Please install certbot manually:"
    Write-Host "  Ubuntu/Debian: sudo apt install certbot" -ForegroundColor Yellow
    Write-Host "  CentOS/RHEL: sudo yum install certbot" -ForegroundColor Yellow
    Write-Host "  Or visit: https://certbot.eff.org/" -ForegroundColor Yellow
    exit 1
}

# Step 5: Stop nginx temporarily to free up port 80
Write-Status "Stopping nginx temporarily for certificate renewal..."
if (-not $SkipDocker) {
    try {
        docker-compose stop nginx
        Write-Success "Docker nginx stopped"
    }
    catch {
        try {
            systemctl stop nginx
            Write-Success "System nginx stopped"
        }
        catch {
            Write-Warning "Failed to stop nginx, continuing anyway..."
        }
    }
}

Start-Sleep -Seconds 5

# Step 6: Renew the certificate
Write-Status "Renewing SSL certificate for maxmedme.com..."
try {
    $renewResult = certbot renew --force-renewal --cert-name maxmedme.com
    Write-Success "Certificate renewed successfully!"
    
    # Check certificate expiration
    Write-Status "Certificate expiration date:"
    $certPath = "/etc/letsencrypt/live/maxmedme.com/cert.pem"
    if (Test-Path $certPath) {
        openssl x509 -in $certPath -noout -dates
    }
    
    # Step 7: Restore original configuration
    Write-Status "Restoring original HTTPS configuration..."
    Copy-Item "docker/nginx/conf.d/$backupName" "docker/nginx/conf.d/app.conf"
    
    # Test nginx configuration
    Write-Status "Testing nginx configuration..."
    if (-not $SkipDocker) {
        try {
            docker-compose exec nginx nginx -t
            Write-Success "Nginx configuration is valid"
        }
        catch {
            Write-Warning "Docker nginx test failed, trying system nginx..."
            try {
                nginx -t
                Write-Success "System nginx configuration is valid"
            }
            catch {
                Write-Error "Nginx configuration test failed"
                Copy-Item "docker/nginx/conf.d/$backupName" "docker/nginx/conf.d/app.conf"
                exit 1
            }
        }
    }
    
    # Restart nginx with HTTPS configuration
    Write-Status "Restarting nginx with HTTPS configuration..."
    if (-not $SkipDocker) {
        try {
            docker-compose up -d nginx
            Write-Success "Docker nginx started with HTTPS"
        }
        catch {
            try {
                systemctl start nginx
                Write-Success "System nginx started with HTTPS"
            }
            catch {
                Write-Error "Failed to start nginx"
            }
        }
    }
    
    Write-Success "SSL certificate fix completed successfully!"
    Write-Success "Your website is now accessible via HTTPS"
    
}
catch {
    Write-Error "Certificate renewal failed"
    Write-Status "Attempting to obtain a new certificate..."
    
    try {
        # Try to obtain a new certificate
        certbot certonly --standalone -d maxmedme.com -d www.maxmedme.com --agree-tos --email $Email --force-renewal
        
        Write-Success "New certificate obtained successfully!"
        
        # Restore original configuration
        Copy-Item "docker/nginx/conf.d/$backupName" "docker/nginx/conf.d/app.conf"
        
        if (-not $SkipDocker) {
            try {
                docker-compose up -d nginx
            }
            catch {
                systemctl start nginx
            }
        }
        
        Write-Success "SSL certificate fix completed successfully!"
    }
    catch {
        Write-Error "Failed to obtain new certificate"
        Write-Warning "Website will remain HTTP-only until certificate is manually renewed"
        
        # Keep the temporary configuration
        if (-not $SkipDocker) {
            try {
                docker-compose up -d nginx
            }
            catch {
                systemctl start nginx
            }
        }
        exit 1
    }
}

# Step 8: Set up automatic renewal (Windows Task Scheduler)
Write-Status "Setting up automatic certificate renewal..."
$taskName = "MaxMed-SSL-Renewal"
$taskAction = New-ScheduledTaskAction -Execute "certbot" -Argument "renew --quiet"
$taskTrigger = New-ScheduledTaskTrigger -Daily -At "12:00 PM"
$taskSettings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries

try {
    Register-ScheduledTask -TaskName $taskName -Action $taskAction -Trigger $taskTrigger -Settings $taskSettings -RunLevel Highest -Force
    Write-Success "Automatic renewal task created in Windows Task Scheduler"
}
catch {
    Write-Warning "Failed to create automatic renewal task. Please set it up manually:"
    Write-Host "  Task Name: $taskName" -ForegroundColor Yellow
    Write-Host "  Command: certbot renew --quiet" -ForegroundColor Yellow
    Write-Host "  Schedule: Daily at 12:00 PM" -ForegroundColor Yellow
}

Write-Host ""
Write-Success "🎉 SSL certificate fix process completed!"
Write-Host ""
Write-Status "Next steps:"
Write-Host "1. Test your website: https://maxmedme.com" -ForegroundColor White
Write-Host "2. Check that HTTPS redirects are working" -ForegroundColor White
Write-Host "3. Monitor certificate expiration: certbot certificates" -ForegroundColor White
Write-Host ""
Write-Status "If you encounter any issues, check the logs:"
if (-not $SkipDocker) {
    Write-Host "- Docker nginx logs: docker-compose logs nginx" -ForegroundColor White
}
Write-Host "- Certbot logs: Check Windows Event Viewer" -ForegroundColor White
