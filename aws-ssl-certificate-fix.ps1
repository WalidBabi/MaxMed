# AWS SSL Certificate Fix Script for MaxMed (PowerShell Version)
# This script will fix the expired SSL certificate issue on AWS

param(
    [string]$Domain = "maxmedme.com",
    [string]$WWWDomain = "www.maxmedme.com",
    [string]$Region = "us-east-1",
    [string]$Email = "admin@maxmedme.com"
)

Write-Host "🔒 AWS SSL Certificate Fix Script for MaxMed" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

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

# Check if AWS CLI is installed
try {
    $awsVersion = aws --version
    Write-Success "AWS CLI found: $awsVersion"
}
catch {
    Write-Error "AWS CLI is not installed. Please install it first:"
    Write-Host "  https://docs.aws.amazon.com/cli/latest/userguide/getting-started-install.html" -ForegroundColor Yellow
    exit 1
}

# Check if AWS credentials are configured
try {
    $callerIdentity = aws sts get-caller-identity
    Write-Success "AWS credentials configured for account: $($callerIdentity.Account)"
}
catch {
    Write-Error "AWS credentials are not configured. Please run:"
    Write-Host "  aws configure" -ForegroundColor Yellow
    exit 1
}

Write-Status "Starting AWS SSL certificate fix process..."

# Step 1: Check current certificate status
Write-Status "Checking current certificate status..."
$currentCert = aws acm list-certificates --region $Region --query "CertificateSummaryList[?DomainName=='$Domain'].CertificateArn" --output text

if ($currentCert) {
    Write-Status "Found existing certificate: $currentCert"
    
    # Check certificate expiration
    $certDetails = aws acm describe-certificate --certificate-arn $currentCert --region $Region
    $expiration = $certDetails.Certificate.NotAfter
    Write-Status "Certificate expires: $expiration"
    
    # Check if certificate is expired
    $expirationDate = [DateTime]::Parse($expiration)
    $currentDate = Get-Date
    
    if ($currentDate -gt $expirationDate) {
        Write-Warning "Certificate has expired. Creating new certificate..."
        $expiredCert = $currentCert
    }
    else {
        Write-Success "Certificate is still valid"
        exit 0
    }
}
else {
    Write-Status "No existing certificate found for $Domain"
}

# Step 2: Request new certificate
Write-Status "Requesting new SSL certificate from AWS Certificate Manager..."
$newCertArn = aws acm request-certificate `
    --domain-name $Domain `
    --subject-alternative-names $WWWDomain `
    --validation-method DNS `
    --region $Region `
    --query "CertificateArn" `
    --output text

if ($LASTEXITCODE -eq 0) {
    Write-Success "New certificate requested: $newCertArn"
}
else {
    Write-Error "Failed to request new certificate"
    exit 1
}

# Step 3: Get DNS validation records
Write-Status "Getting DNS validation records..."
Start-Sleep -Seconds 10  # Wait for certificate to be created

$validationRecords = aws acm describe-certificate `
    --certificate-arn $newCertArn `
    --region $Region `
    --query "Certificate.DomainValidationOptions[].ResourceRecord" `
    --output json

if (-not $validationRecords -or $validationRecords -eq "[]") {
    Write-Error "No validation records found. Certificate may not be ready yet."
    exit 1
}

Write-Status "DNS validation records:"
$validationRecords | ConvertFrom-Json | ForEach-Object {
    Write-Host "Name: $($_.Name), Value: $($_.Value), Type: $($_.Type)" -ForegroundColor White
}

# Step 4: Check if using Route 53 for DNS
Write-Status "Checking if Route 53 is used for DNS..."
$hostedZone = aws route53 list-hosted-zones --query "HostedZones[?Name=='$Domain.'].Id" --output text

if ($hostedZone) {
    Write-Status "Found Route 53 hosted zone: $hostedZone"
    
    # Extract hosted zone ID (remove /hostedzone/ prefix)
    $hostedZoneId = $hostedZone -replace "/hostedzone/", ""
    
    # Create validation records in Route 53
    Write-Status "Creating DNS validation records in Route 53..."
    
    $validationRecords | ConvertFrom-Json | ForEach-Object {
        $changeBatch = @{
            Changes = @(
                @{
                    Action = "UPSERT"
                    ResourceRecordSet = @{
                        Name = $_.Name
                        Type = $_.Type
                        TTL = 300
                        ResourceRecords = @(
                            @{
                                Value = $_.Value
                            }
                        )
                    }
                }
            )
        }
        
        $changeBatchJson = $changeBatch | ConvertTo-Json -Depth 10
        $changeBatchJson | Out-File -FilePath "validation-changes.json" -Encoding UTF8
        
        aws route53 change-resource-record-sets `
            --hosted-zone-id $hostedZoneId `
            --change-batch file://validation-changes.json
        
        if ($LASTEXITCODE -eq 0) {
            Write-Success "DNS validation records created in Route 53"
        }
        else {
            Write-Error "Failed to create DNS validation records"
            exit 1
        }
        
        Remove-Item "validation-changes.json" -ErrorAction SilentlyContinue
    }
}
else {
    Write-Warning "Route 53 hosted zone not found for $Domain"
    Write-Status "Please manually add the following DNS records to your DNS provider:"
    $validationRecords | ConvertFrom-Json | ForEach-Object {
        Write-Host "Name: $($_.Name), Value: $($_.Value), Type: $($_.Type)" -ForegroundColor White
    }
}

# Step 5: Wait for certificate validation
Write-Status "Waiting for certificate validation..."
do {
    $status = aws acm describe-certificate `
        --certificate-arn $newCertArn `
        --region $Region `
        --query "Certificate.Status" `
        --output text
    
    Write-Status "Certificate status: $status"
    
    if ($status -eq "ISSUED") {
        Write-Success "Certificate has been issued successfully!"
        break
    }
    elseif ($status -eq "FAILED") {
        Write-Error "Certificate validation failed"
        exit 1
    }
    elseif ($status -eq "PENDING_VALIDATION") {
        Write-Status "Certificate is pending validation. Waiting 30 seconds..."
        Start-Sleep -Seconds 30
    }
    else {
        Write-Status "Unknown status: $status. Waiting 30 seconds..."
        Start-Sleep -Seconds 30
    }
} while ($true)

# Step 6: Update Load Balancer or CloudFront distribution
Write-Status "Checking for Load Balancers..."
$albList = aws elbv2 describe-load-balancers --query "LoadBalancers[?DNSName=='$Domain' || DNSName=='$WWWDomain'].LoadBalancerArn" --output text

if ($albList) {
    Write-Status "Found Application Load Balancer(s): $albList"
    
    $albList -split "`n" | ForEach-Object {
        $albArn = $_.Trim()
        if ($albArn) {
            Write-Status "Updating ALB listener with new certificate..."
            
            # Get listener ARN
            $listenerArn = aws elbv2 describe-listeners `
                --load-balancer-arn $albArn `
                --query "Listeners[?Port==443].ListenerArn" `
                --output text
            
            if ($listenerArn) {
                aws elbv2 modify-listener `
                    --listener-arn $listenerArn `
                    --certificates CertificateArn=$newCertArn
                
                if ($LASTEXITCODE -eq 0) {
                    Write-Success "Updated ALB listener with new certificate"
                }
                else {
                    Write-Error "Failed to update ALB listener"
                }
            }
        }
    }
}

# Check for CloudFront distributions
Write-Status "Checking for CloudFront distributions..."
$cfDistributions = aws cloudfront list-distributions `
    --query "DistributionList.Items[?Aliases.Items[?contains(@, '$Domain')]].Id" `
    --output text

if ($cfDistributions) {
    Write-Status "Found CloudFront distribution(s): $cfDistributions"
    
    $cfDistributions -split "`n" | ForEach-Object {
        $distId = $_.Trim()
        if ($distId) {
            Write-Status "Updating CloudFront distribution with new certificate..."
            
            # Get current distribution config
            aws cloudfront get-distribution-config --id $distId > cf-config.json
            
            # Update certificate ARN in config
            $config = Get-Content cf-config.json | ConvertFrom-Json
            $config.DistributionConfig.ViewerCertificate.ACMCertificateArn = $newCertArn
            $config | ConvertTo-Json -Depth 20 > cf-config-updated.json
            
            # Update distribution
            $etag = $config.ETag
            aws cloudfront update-distribution `
                --id $distId `
                --distribution-config file://cf-config-updated.json `
                --if-match $etag
            
            if ($LASTEXITCODE -eq 0) {
                Write-Success "Updated CloudFront distribution with new certificate"
            }
            else {
                Write-Error "Failed to update CloudFront distribution"
            }
            
            Remove-Item "cf-config.json", "cf-config-updated.json" -ErrorAction SilentlyContinue
        }
    }
}

# Step 7: Clean up expired certificate (optional)
if ($expiredCert) {
    Write-Status "Cleaning up expired certificate..."
    aws acm delete-certificate --certificate-arn $expiredCert --region $Region
    
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Expired certificate deleted"
    }
    else {
        Write-Warning "Failed to delete expired certificate (may be in use)"
    }
}

# Step 8: Set up automatic renewal monitoring
Write-Status "Setting up certificate monitoring..."

# Create CloudWatch alarm for certificate expiration
$alarmName = "MaxMed-SSL-Certificate-Expiration"
$accountId = (aws sts get-caller-identity --query Account --output text)

aws cloudwatch put-metric-alarm `
    --alarm-name $alarmName `
    --alarm-description "SSL Certificate expiration warning for $Domain" `
    --metric-name "DaysToExpiry" `
    --namespace "AWS/CertificateManager" `
    --statistic "Minimum" `
    --period 86400 `
    --threshold 30 `
    --comparison-operator "LessThanThreshold" `
    --evaluation-periods 1 `
    --alarm-actions "arn:aws:sns:us-east-1:$accountId`:MaxMed-Alerts" `
    --region $Region

if ($LASTEXITCODE -eq 0) {
    Write-Success "CloudWatch alarm created for certificate monitoring"
}
else {
    Write-Warning "Failed to create CloudWatch alarm (SNS topic may not exist)"
}

Write-Success "🎉 AWS SSL certificate fix completed successfully!"
Write-Host ""
Write-Status "Next steps:"
Write-Host "1. Test your website: https://$Domain" -ForegroundColor White
Write-Host "2. Verify HTTPS redirects are working" -ForegroundColor White
Write-Host "3. Monitor certificate status in AWS Console" -ForegroundColor White
Write-Host "4. Set up SNS notifications for certificate alerts" -ForegroundColor White
Write-Host ""
Write-Status "Certificate ARN: $newCertArn"
Write-Status "Region: $Region"
